<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class CreateReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'content',
        'date',
        'pinned',
        'response',
        'title',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'author' => '',
            'avatar' => '',
            'content' => '',
            'custom' => '',
            'date' => '',
            'email' => '',
            'ip_address' => '',
            'pinned' => false,
            'response' => '',
            'title' => '',
            'url' => '',
        ];
    }
}
