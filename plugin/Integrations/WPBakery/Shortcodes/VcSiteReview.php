<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class VcSiteReview extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-review.svg');
    }
}
