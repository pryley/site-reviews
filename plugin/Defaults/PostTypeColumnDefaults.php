<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PostTypeColumnDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'cb' => '',
            'title' => '',
            'category' => '',
            'assigned_to' => _x('Assigned To', 'admin-text', 'site-reviews'),
            'reviewer' => _x('Author', 'admin-text', 'site-reviews'),
            'email' => _x('Email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
            'response' => _x('Response', 'admin-text', 'site-reviews'),
            'review_type' => _x('Type', 'admin-text', 'site-reviews'),
            'rating' => _x('Rating', 'admin-text', 'site-reviews'),
            'pinned' => _x('Pinned', 'admin-text', 'site-reviews'),
            'date' => '',
        ];
    }
}
