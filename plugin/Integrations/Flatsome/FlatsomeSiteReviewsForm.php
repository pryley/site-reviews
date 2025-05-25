<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FlatsomeSiteReviewsForm extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-form.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }
}
