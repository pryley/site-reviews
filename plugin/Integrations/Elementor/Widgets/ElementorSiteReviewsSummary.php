<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class ElementorSiteReviewsSummary extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-summary';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
