<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FusionSiteReviewsSummary extends FusionElement
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    protected static function shortcodeIcon(): string
    {
        return 'fusion-glsr-summary';
    }

    protected static function styleConfig(): array
    {
        return [];
    }
}
