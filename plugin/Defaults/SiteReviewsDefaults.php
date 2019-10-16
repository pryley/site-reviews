<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'fallback',
        'title',
    ];

    /**
     * @var array
     */
    protected $mapped = [
        'count' => 'display', // @deprecated since v4.1.0
        'per_page' => 'display',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_to' => '',
            'category' => '',
            'class' => '',
            'display' => 5,
            'fallback' => '',
            'hide' => [],
            'id' => '',
            'offset' => '',
            'pagination' => false,
            'rating' => 0,
            'schema' => false,
            'title' => '',
            'type' => 'local',
        ];
    }
}
