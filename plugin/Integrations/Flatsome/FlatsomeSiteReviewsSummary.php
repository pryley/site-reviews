<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FlatsomeSiteReviewsSummary extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-summary.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
