<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ReviewerDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'points' => 'int',
        'remove_on_trash' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'log' => 'text',
        'per_day' => 'min:0',
        'per_post' => 'min:0',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'log' => '%plural% for writing a review',
            'per_day' => 0,
            'per_post' => 1,
            'points' => 1,
            'remove_on_trash' => false,
        ];
    }
}
