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
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_to' => '',
            'category' => '',
            'class' => '',
            'count' => 5,
            'fallback' => '',
            'hide' => [],
            'id' => '',
            'offset' => '',
            'pagination' => false,
            'rating' => 1,
            'schema' => false,
            'title' => '',
            'type' => 'local',
        ];
    }
}
