<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class RatingDefaults extends Defaults
{
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
