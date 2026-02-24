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
            'style_max_width' => [
                'description' => esc_attr_x('Enter value including any valid CSS unit, e.g. 100%.', 'admin-text', 'site-reviews'),
                'heading' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'type' => 'textfield',
            ],
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
