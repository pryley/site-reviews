<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FusionSiteReviewsSummary extends FusionElement
{
    public function add_css_files()
    {
        FusionBuilder()->add_element_css(glsr()->path('assets/blocks/site_reviews_summary/style-index.css'));
    }

    public function elementIcon(): string
    {
        return 'fusion-glsr-summary';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
            'style_bar_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
        ];
    }
}
