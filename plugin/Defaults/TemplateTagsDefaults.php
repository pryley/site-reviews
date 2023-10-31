<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TemplateTagsDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'admin_email' => _x('admin email', 'template tag button text', 'site-reviews'),
            'approve_url' => _x('approve url', 'template tag button text', 'site-reviews'),
            'edit_url' => _x('edit url', 'template tag button text', 'site-reviews'),
            'review_assigned_links' => _x('assigned links', 'template tag button text', 'site-reviews'),
            'review_assigned_posts' => _x('assigned posts', 'template tag button text', 'site-reviews'),
            'review_assigned_users' => _x('assigned users', 'template tag button text', 'site-reviews'),
            'review_author' => _x('name', 'template tag button text', 'site-reviews'),
            'review_categories' => _x('categories', 'template tag button text', 'site-reviews'),
            'review_content' => _x('content', 'template tag button text', 'site-reviews'),
            'review_email' => _x('email', 'template tag button text', 'site-reviews'),
            'review_id' => _x('review id', 'template tag button text', 'site-reviews'),
            'review_ip' => _x('ip address', 'template tag button text', 'site-reviews'),
            'review_rating' => _x('rating', 'template tag button text', 'site-reviews'),
            'review_response' => _x('response', 'template tag button text', 'site-reviews'),
            'review_stars' => _x('stars', 'template tag button text', 'site-reviews'),
            'review_title' => _x('title', 'template tag button text', 'site-reviews'),
            'site_title' => _x('site title', 'template tag button text', 'site-reviews'),
            'site_url' => _x('site url', 'template tag button text', 'site-reviews'),
            'verify_url' => _x('verify url', 'template tag button text', 'site-reviews'),
            'verified_date' => _x('verified date', 'template tag button text', 'site-reviews'),
        ];
    }
}
