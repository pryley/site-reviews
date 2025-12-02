<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class ElementorSiteReviewsForm extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-form';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }
}
