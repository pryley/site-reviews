<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FusionSiteReviewsForm extends FusionElement
{
    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    protected static function shortcodeIcon(): string
    {
        return 'fusion-glsr-form';
    }
}
