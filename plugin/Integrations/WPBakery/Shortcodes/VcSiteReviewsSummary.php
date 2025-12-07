<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class VcSiteReviewsSummary extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-summary.svg');
    }
}
