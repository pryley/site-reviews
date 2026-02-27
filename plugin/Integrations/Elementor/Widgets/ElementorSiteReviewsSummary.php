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
        return [ // order is intentional
            'style_align' => [
                'group' => 'summary',
                'label' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'icon' => 'eicon-flex eicon-align-start-h',
                        'title' => esc_html_x('Start', 'admin-text', 'site-reviews'),
                    ],
                    'center' => [
                        'icon' => 'eicon-flex eicon-align-center-h',
                        'title' => esc_html_x('Center', 'admin-text', 'site-reviews'),
                    ],
                    'right' => [
                        'icon' => 'eicon-flex eicon-align-end-h',
                        'title' => esc_html_x('End', 'admin-text', 'site-reviews'),
                    ],
                ],
                'prefix_class' => 'items-justified-',
                'type' => Controls_Manager::CHOOSE,
            ],
            'style_max_width' => [
                'default' => [
                    'unit' => '%',
                ],
                'group' => 'summary',
                'is_responsive' => true,
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '.glsr-elementor-{{ID}}' => '--glsr-max-w: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['%', 'px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'group' => 'summary_rating',
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
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
                'group' => 'summary_bars',
                'label' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'selectors' => [
                    '.glsr-elementor-{{ID}}' => '--glsr-bar-bg: {{VALUE}}',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }
}
