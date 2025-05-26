<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FusionSiteReviews extends FusionElement
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    protected static function shortcodeIcon(): string
    {
        return 'fusion-glsr-reviews';
    }

    protected static function styleConfig(): array
    {
        return [];
    }
}
