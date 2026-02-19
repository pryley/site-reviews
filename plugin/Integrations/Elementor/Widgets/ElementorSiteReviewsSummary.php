<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class ElementorSiteReviewsSummary extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-summary';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews-themes'),
                'label_block' => false,
                'selectors' => [
                    '.glsr-elementor-{{ID}} .glsr:not([data-theme])' => '--glsr-summary-star-bg: {{VALUE}}',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'style_bar_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Bar Color', 'admin-text', 'site-reviews-themes'),
                'label_block' => false,
                'selectors' => [
                    '.glsr-elementor-{{ID}}' => '--glsr-bar-bg: {{VALUE}}',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }
}
