<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;

class RatingManager
{
    /**
     * @param bool $flatten
     * @return array
     */
    public function ratings(array $args = [])
    {
        $args = wp_parse_args($args, [
            'max' => glsr()->constant('MAX_RATING', Rating::class),
            'min' => glsr()->constant('MIN_RATING', Rating::class),
        ]);
        $ratings = [];
        $results = glsr(Query::class)->ratings($args);
        array_walk_recursive($results, function ($rating, $index) use (&$ratings) {
            $ratings[$index] = $rating + intval(Arr::get($ratings, $index, 0));
        });
        foreach ($ratings as $index => &$rating) {
            if (!Helper::inRange($index, $args['min'], $args['max'])) {
                $rating = 0;
            }
        }
        return $ratings;
    }
}
