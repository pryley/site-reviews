<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

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

    protected function styles(): array
    {
        return [
            'site-reviews-form-style' => glsr()->url('assets/blocks/site_reviews_form/style-index.css'),
        ];
    }
}
