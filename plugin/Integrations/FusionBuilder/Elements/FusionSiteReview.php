<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FusionSiteReview extends FusionElement
{
    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    protected static function shortcodeIcon(): string
    {
        return 'fusion-glsr-review';
    }
}
