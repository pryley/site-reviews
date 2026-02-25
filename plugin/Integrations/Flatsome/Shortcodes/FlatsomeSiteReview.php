<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

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

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'alpha' => true,
                'format' => 'rgb',
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'helpers' => require(get_template_directory().'/inc/builder/shortcodes/helpers/colors.php'),
                'position' => 'bottom right',
                'type' => 'colorpicker',
            ],
        ];
    }

    protected function styles(): array
    {
        return [
            'site-reviews-review-style' => glsr()->url('assets/blocks/site_review/style-index.css'),
        ];
    }
}
