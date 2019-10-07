<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;

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
            'date' => current_time('mysql'),
            'email' => '',
            'ip_address' => Helper::getIpAddress(),
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
