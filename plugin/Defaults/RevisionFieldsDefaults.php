<?php

namespace GeminiLabs\SiteReviews\Defaults;

class RevisionFieldsDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [ // order is intentional
            'response' => _x('Response', 'admin-text', 'site-reviews'),
            'rating' => _x('Rating', 'admin-text', 'site-reviews'),
            'type' => _x('Type', 'admin-text', 'site-reviews'),
            'name' => _x('Name', 'admin-text', 'site-reviews'),
            'email' => _x('Email', 'admin-text', 'site-reviews'),
            'ip_address' => _x('IP Address', 'admin-text', 'site-reviews'),
            'avatar' => _x('Avatar', 'admin-text', 'site-reviews'),
            'url' => _x('Url', 'admin-text', 'site-reviews'),
        ];
    }
}
