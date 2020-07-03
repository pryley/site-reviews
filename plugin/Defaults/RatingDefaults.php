<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class RatingDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $casts = [
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'rating' => 'int',
        'review_id' => 'int',
        'type' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'avatar' => '',
            'email' => '',
            'ip_address' => '',
            'is_approved' => '',
            'is_pinned' => '',
            'name' => '',
            'rating' => '',
            'review_id' => '',
            'type' => '',
            'url' => '',
        ];
    }
}
