<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class StarRatingDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'args' => 'array',
        'prefix' => 'string',
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
            'prefix' => glsr()->isAdmin() ? '' : 'glsr-',
            'rating' => 0,
            'reviews' => 0,
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        $values['rating'] = sprintf('%g', Arr::get($values, 'rating', 0));
        return $values;
    }
}
