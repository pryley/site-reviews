<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class RatingDefaults extends Defaults
{
    public $sanitize = [
        'avatar' => 'url',
        'email' => 'email',
        'ip_address' => 'text',
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'name' => 'text',
        'rating' => 'int',
        'review_id' => 'int',
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
            'ip_address' => '',
            'is_approved' => false,
            'is_pinned' => false,
            'name' => '',
            'rating' => '',
            'review_id' => '',
            'type' => '',
            'url' => '',
        ];
    }
}
