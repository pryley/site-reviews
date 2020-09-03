<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsShortcode extends Shortcode
{
    protected function hideOptions()
    {
        return [
            'title' => _x('Hide the title', 'admin-text', 'site-reviews'),
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'date' => _x('Hide the date', 'admin-text', 'site-reviews'),
            'assigned_links' => _x('Hide the assigned links (if shown)', 'admin-text', 'site-reviews'),
            'content' => _x('Hide the content', 'admin-text', 'site-reviews'),
            'avatar' => _x('Hide the avatar (if shown)', 'admin-text', 'site-reviews'),
            'author' => _x('Hide the author', 'admin-text', 'site-reviews'),
            'response' => _x('Hide the response', 'admin-text', 'site-reviews'),
        ];
    }
}
