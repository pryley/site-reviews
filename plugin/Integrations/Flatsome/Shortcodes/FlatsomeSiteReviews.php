<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

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

    protected function styles(): array
    {
        return [
            'site-reviews-reviews-style' => glsr()->url('assets/blocks/site_reviews/style-index.css'),
        ];
    }
}
