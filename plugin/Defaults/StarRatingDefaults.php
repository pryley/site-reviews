<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Modules\Rating;

class StarRatingDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'args' => 'array',
        'rating' => 'float',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'reviews' => 'min:0',
    ];

    protected function defaults(): array
    {
        return [
            'args' => [],
            'max_rating' => 0,
            'num_empty' => 0,
            'num_full' => 0,
            'num_half' => 0,
            'rating' => 0,
            'reviews' => 0,
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $rating = $values['rating'] ?? 0;
        $reviews = $values['reviews'] ?? 0;
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $numFull = intval(floor($rating));
        $numHalf = intval(ceil($rating - $numFull));
        $numEmpty = max(0, $maxRating - $numFull - $numHalf);
        $values['num_empty'] = $numEmpty;
        $values['num_full'] = $numFull;
        $values['num_half'] = $numHalf;
        if (0 === $reviews && 0 === $numHalf) {
            $values['rating'] = $numFull; // integer
        }
        return $values;
    }
}
