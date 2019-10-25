<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class CreateReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $guarded = [
        'assigned_to',
        'content',
        'date',
        'pinned',
        'response',
        'review_id',
        'review_type',
        'title',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_to' => '',
            'author' => '',
            'avatar' => '',
            'content' => '',
            'custom' => '',
            'date' => '',
            'email' => '',
            'ip_address' => '',
            'pinned' => false,
            'rating' => '',
            'response' => '',
            'review_id' => md5(time().mt_rand()),
            'review_type' => 'local',
            'title' => '',
            'url' => '',
        ];
    }
}
