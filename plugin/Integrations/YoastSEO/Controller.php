<?php

namespace GeminiLabs\SiteReviews\Integrations\YoastSEO;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    protected array $allowedTypes;

    /**
     * @param array $graph
     *
     * @filter wpseo_schema_graph
     */
    public function filterSchema($graph): array
    {
        $graph = Arr::consolidate($graph);
        if (function_exists('is_product') && is_product()) {
            return $graph; // skip WooCommerce products
        }
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $graph;
        }
        $key = $this->ratedNodeKey($graph);
        if (null === $key) {
            return $graph;
        }
        $graph[$key] = glsr(Schema::class)->rate(Arr::consolidate($graph[$key]), $schema);
        return $graph;
    }

    /**
     * @see https://developers.google.com/search/docs/appearance/structured-data/review-snippet
     */
    protected function allowedTypes(): array
    {
        return $this->allowedTypes ??= glsr(RatingSchemaTypeDefaults::class)->defaults();
    }

    protected function isOrganizationId(string $id): bool
    {
        $fragment = strtolower((string) parse_url($id, \PHP_URL_FRAGMENT));
        return 'organization' === $fragment;
    }

    protected function isRatedNode(array $node): bool
    {
        $types = Arr::getAs('array', $node, '@type');
        if (empty(array_intersect($types, $this->allowedTypes()))) {
            return false;
        }
        return '' !== trim(Arr::getAs('string', $node, 'name'));
    }

    /**
     * @return int|string|null
     */
    protected function nodeKey(array $graph, callable $isCandidate)
    {
        foreach ($graph as $key => $node) {
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
    protected function primaryContentKey(array $graph)
    {
        return $this->nodeKey($graph, fn ($node) => !empty($node['mainEntityOfPage']));
    }

    /**
     * @return int|string|null
     */
    protected function publisherKey(array $graph)
    {
        $ids = [];
        foreach ($graph as $node) {
            if ($id = Arr::getAs('string', $node, 'publisher.@id')) {
                $ids[] = $id;
            }
        }
        $key = $this->nodeKey($graph, fn ($node) => in_array(Arr::get($node, '@id'), $ids));
        return $key ?? $this->nodeKey($graph,
            fn ($node) => $this->isOrganizationId(Arr::getAs('string', $node, '@id'))
        );
    }

    /**
     * @return int|string|null
     */
    protected function ratedNodeKey(array $graph)
    {
        return glsr(Schema::class)->isSiteWide()
            ? $this->publisherKey($graph)
            : $this->primaryContentKey($graph);
    }
}
