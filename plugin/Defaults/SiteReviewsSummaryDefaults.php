<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsSummaryDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'labels',
        'text',
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
            'hide' => '',
            'id' => '',
            'labels' => '',
            'rating' => 1,
            'schema' => false,
            'text' => '',
            'title' => '',
            'type' => 'local',
        ];
    }
}
