<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\ReviewTagDefaults;
use GeminiLabs\SiteReviews\Defaults\ReviewTagsDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;

class ReviewTags
{
    /**
     * Guards the mock build in contextNames() against a filter that asks
     * for the tags while the review that answers that question is still
     * being built.
     */
    protected static bool $isBuilding = false;

    public function all(): array
    {
        $cached = glsr()->retrieveAs('array', 'review_tags', []);
        if (!empty($cached)) {
            return $cached;
        }
        $tags = glsr(ReviewTagsDefaults::class)->defaults();
        $undescribed = glsr(ReviewTagDefaults::class)->defaults();
        foreach ($this->contextNames() as $name) {
            $tags[$name] ??= $undescribed;
        }
        ksort($tags);
        glsr()->store('review_tags', $tags);
        return $tags;
    }

    /**
     * The tags that can be used in a review layout builder.
     */
    public function displayable(): array
    {
        return array_filter($this->all(), fn ($tag) => Cast::toBool($tag['display']));
    }

    /**
     * The tags that can be used in a review template editor.
     */
    public function insertable(): array
    {
        return array_filter($this->all(), fn ($tag) => Cast::toBool($tag['insert']));
    }

    /**
     * The insertable tags filed under a heading — "default" is the
     * handful an author reaches for, "other" everything else.
     */
    public function group(string $group): array
    {
        return array_filter($this->insertable(), fn ($tag) => $group === $tag['group']);
    }

    /**
     * Every reserved tag name (these cannot be use as custom field names).
     */
    public function names(): array
    {
        return array_keys($this->all());
    }

    /**
     * The context an actual build produces. A tag that arrives through
     * review/build/after alone is still a tag; this is what keeps the
     * registry a description rather than a gate.
     */
    protected function contextNames(): array
    {
        if (static::$isBuilding) {
            return array_keys(glsr(ReviewTagsDefaults::class)->defaults());
        }
        try {
            static::$isBuilding = true;
            return array_keys(glsr_get_review(0)->build()->context);
        } finally {
            static::$isBuilding = false;
        }
    }
}
