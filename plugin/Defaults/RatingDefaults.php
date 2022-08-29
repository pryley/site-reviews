<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class RatingDefaults extends Defaults
{
    /**
     * @var string[]
     */
    public $guarded = [
        'ID',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'avatar' => 'url',
        'email' => 'email',
        'ID' => 'int',
        'ip_address' => 'text',
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'is_verified' => 'bool',
        'name' => 'text',
        'rating' => 'int',
        'review_id' => 'int',
        'score' => 'int',
        'terms' => 'bool',
        'type' => 'text',
        'url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'avatar' => '',
            'email' => '',
            'ID' => '',
            'ip_address' => '',
            'is_approved' => false,
            'is_pinned' => false,
            'is_verified' => false,
            'name' => '',
            'rating' => '',
            'review_id' => '',
            'score' => '',
            'terms' => true,
            'type' => '',
            'url' => '',
        ];
    }
}
