<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;

class RatingManager
{
    public function ratings(array $args = []): array
    {
        $ratings = glsr(Query::class)->ratings($args);
        $ratings = $this->reduce($ratings, $args);
        return glsr()->filterArray('ratings', $ratings, $args);
    }

    public function ratingsGroupedBy(string $metaGroup, array $args = []): array
    {
        $metaGroup = strtolower(Str::restrictTo(['post', 'term', 'user'], $metaGroup, 'post'));
        $metaTable = sprintf('%smeta', $metaGroup);
        $ratings = glsr(Query::class)->ratingsFor($metaTable, $args);
        foreach ($ratings as $id => &$group) {
            $group = $this->reduce($group, $args);
        }
        return glsr()->filterArray('ratings/grouped', $ratings, $metaGroup, $args);
    }

    protected function maxRating(array $args): int
    {
        return Arr::getAs('int', $args, 'max', glsr()->constant('MAX_RATING', Rating::class));
    }

    protected function minRating(array $args): int
    {
        return Arr::getAs('int', $args, 'min', glsr()->constant('MIN_RATING', Rating::class));
    }

    /**
     * Combine ratings grouped by type into a single rating array.
     */
    protected function reduce(array $ratings, array $args = []): array
    {
        $max = $this->maxRating($args);
        $min = $this->minRating($args);
        $normalized = [];
        array_walk_recursive($ratings, function ($rating, $index) use (&$normalized) {
            $normalized[$index] = $rating + intval(Arr::get($normalized, $index, 0));
        });
        foreach ($normalized as $index => &$rating) {
            if (!Helper::inRange($index, $min, $max)) {
                $rating = 0;
            }
        }
        return $normalized;
    }
}
