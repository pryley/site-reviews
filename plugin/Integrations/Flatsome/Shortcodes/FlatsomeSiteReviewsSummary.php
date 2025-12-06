<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

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

    protected function styles(): array
    {
        return [
            'site-reviews-summary-style' => glsr()->url('assets/blocks/site_reviews_summary/style-index.css'),
        ];
    }
}
