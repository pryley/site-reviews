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
        'is_block_editor',
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
            'assigned_to_custom' => '',
            'category' => '',
            'class' => '',
            'display' => 5,
            'fallback' => '',
            'hide' => [],
            'id' => '',
            'is_block_editor' => false,
            'offset' => '',
            'page' => 1,
            'pagination' => false,
            'rating' => 0,
            'schema' => false,
            'title' => '',
            'type' => 'local',
            'user' => '',
        ];
    }
}
