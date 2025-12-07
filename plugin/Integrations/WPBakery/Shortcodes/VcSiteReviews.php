<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class VcSiteReviews extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-reviews.svg');
    }
}
