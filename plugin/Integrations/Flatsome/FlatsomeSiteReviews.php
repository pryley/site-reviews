<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FlatsomeSiteReviews extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-reviews.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }
}
