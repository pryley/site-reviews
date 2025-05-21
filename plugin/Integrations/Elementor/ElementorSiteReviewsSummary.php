<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

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
            'alignment' => [
                'default' => 'start',
                'is_responsive' => true,
                'label' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'options' => [
                    'start' => [
                        'title' => esc_html_x('Start', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-start-h',
                    ],
                    'center' => [
                        'title' => esc_html_x('Center', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-center-h',
                    ],
                    'end' => [
                        'title' => esc_html_x('End', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-flex eicon-align-end-h',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr-summary-text' => 'text-align: {{VALUE}};',
                ],
                'type' => Controls_Manager::CHOOSE,
            ],
            'max_width' => [
                'default' => [
                    'unit' => 'px',
                    'size' => 450,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'range' => [
                    '%' => [
                        'min' => 50,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 50,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-max-w: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', '%', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'percentage_bar_height' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Percent Bar Height', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'max' => 1.5,
                        'min' => 0.1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-bar-size: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'percentage_bar_spacing' => [
                'default' => [
                    'size' => 1.5,
                    'unit' => 'em',
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Percent Bar Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'max' => 2,
                        'min' => 1,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-bar-spacing: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-bar-background-percent' => '--glsr-bar-bg: {{VALUE}} !important',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-empty' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-full' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-star-half' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1.5,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 0.25,
                        'max' => 2.25,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-summary-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
