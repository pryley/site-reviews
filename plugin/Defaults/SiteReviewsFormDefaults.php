<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class SiteReviewsFormDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'description',
        'is_block_editor',
        'title',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assign_to' => '',
            'assign_to_custom' => '',
            'category' => '',
            'class' => '',
            'description' => '',
            'excluded' => '',
            'hide' => '',
            'id' => '',
            'is_block_editor' => false,
            'title' => '',
            'user' => '',
        ];
    }
}
