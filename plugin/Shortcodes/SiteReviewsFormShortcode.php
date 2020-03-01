<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsFormShortcode extends Shortcode
{
    protected function hideOptions()
    {
        return [
            'rating' => _x('Hide the rating field', 'admin-text', 'site-reviews'),
            'title' => _x('Hide the title field', 'admin-text', 'site-reviews'),
            'content' => _x('Hide the review field', 'admin-text', 'site-reviews'),
            'name' => _x('Hide the name field', 'admin-text', 'site-reviews'),
            'email' => _x('Hide the email field', 'admin-text', 'site-reviews'),
            'terms' => _x('Hide the terms field', 'admin-text', 'site-reviews'),
        ];
    }
}
