<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FlatsomeSiteReview extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-review.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }
}
