<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

class SiteReviewsSummaryShortcode extends Shortcode
{
    protected function hideOptions()
    {
        return [
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'stars' => _x('Hide the stars', 'admin-text', 'site-reviews'),
            'summary' => _x('Hide the summary', 'admin-text', 'site-reviews'),
            'bars' => _x('Hide the percentage bars', 'admin-text', 'site-reviews'),
            'if_empty' => _x('Hide if no reviews are found', 'admin-text', 'site-reviews'),
        ];
    }
}
