<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FusionSiteReviews extends FusionElement
{
    public function add_css_files()
    {
        FusionBuilder()->add_element_css(glsr()->path('assets/blocks/site_reviews/style-index.css'));
    }

    public function elementIcon(): string
    {
        return 'fusion-glsr-reviews';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
        ];
    }
}
