<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class ElementorSiteReview extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-review';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }
}
