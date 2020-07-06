<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsSummaryDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'is_block_editor',
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
            'assigned_to_custom' => '',
            'category' => '',
            'class' => '',
            'hide' => '',
            'id' => '',
            'is_block_editor' => false,
            'labels' => '',
            'rating' => 1,
            'schema' => false,
            'text' => '',
            'title' => '',
            'type' => 'local',
            'user' => '',
        ];
    }
}
