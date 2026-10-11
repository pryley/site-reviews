<?php

namespace GeminiLabs\SiteReviews\Integrations\YoastSEO;

use GeminiLabs\SiteReviews\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;

/**
 * Finds the node of Yoast's page graph that a rating belongs to.
 */
class Graph
{
    protected array $allowedTypes;
    protected array $graph;

    public function __construct(array $graph)
    {
        $this->allowedTypes = glsr(RatingSchemaTypeDefaults::class)->defaults();
        $this->graph = $graph;
    }

    /**
     * @return int|string|null
     */
    public function ratedKey()
    {
        return glsr(Schema::class)->isSiteWide()
            ? $this->publisherKey()
            : $this->primaryContentKey();
    }

    protected function isOrganizationId(string $id): bool
    {
        $fragment = strtolower((string) parse_url($id, \PHP_URL_FRAGMENT));
        return 'organization' === $fragment;
    }

    /**
     * @see https://developers.google.com/search/docs/appearance/structured-data/review-snippet
     */
    protected function isRatedNode(array $node): bool
    {
        $types = Arr::getAs('array', $node, '@type');
        if (empty(array_intersect($types, $this->allowedTypes))) {
            return false;
        }
        return '' !== trim(Arr::getAs('string', $node, 'name'));
    }

    /**
     * @return int|string|null
     */
    protected function nodeKey(callable $isCandidate)
    {
        foreach ($this->graph as $key => $node) {
            $node = Arr::consolidate($node);
            if ($isCandidate($node) && $this->isRatedNode($node)) {
                return $key;
            }
        }
        return null;
    }

    /**
     * @return int|string|null
     */
    protected function primaryContentKey()
    {
        return $this->nodeKey(fn ($node) => !empty($node['mainEntityOfPage']));
    }

    /**
     * @return int|string|null
     */
    protected function publisherKey()
    {
        $ids = [];
        foreach ($this->graph as $node) {
            if ($id = Arr::getAs('string', $node, 'publisher.@id')) {
                $ids[] = $id;
            }
        }
        $key = $this->nodeKey(fn ($node) => in_array(Arr::get($node, '@id'), $ids));
        return $key ?? $this->nodeKey(
            fn ($node) => $this->isOrganizationId(Arr::getAs('string', $node, '@id'))
        );
    }
}
