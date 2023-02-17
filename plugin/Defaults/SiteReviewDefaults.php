<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'debug' => 'bool',
        'hide' => 'array',
        'post_id' => 'int',
    ];

    /**
     * @var string[]
     */
    public $guarded = [
        'fallback',
        'title',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'id' => 'id',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'debug' => false,
            'fallback' => __('Review not found.', 'site-reviews'),
            'hide' => [],
            'id' => '',
            'post_id' => 0,
            'title' => '',
        ];
    }
}
