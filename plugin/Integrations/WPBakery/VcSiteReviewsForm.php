<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class VcSiteReviewsForm extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-form.svg');
    }
}
