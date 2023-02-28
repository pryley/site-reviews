<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class StarRatingDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'args' => 'array',
        'prefix' => 'string',
        'rating' => 'float',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'reviews' => 'min:0',
    ];

    /**
     * @return array
     */
    protected function defaults()
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
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $values['rating'] = sprintf('%g', Arr::get($values, 'rating', 0));
        return $values;
    }
}
