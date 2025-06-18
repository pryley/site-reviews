<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use GeminiLabs\SiteReviews\License;
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

    protected function get_upsale_data(): array
    {
        return [
            'condition' => !glsr(License::class)->isPremium(),
            'description' => esc_html_x('Upgrade to Site Reviews Premium and get a bunch of additional features and professional support.', 'admin-text', 'site-reviews'),
            'image' => glsr()->url('assets/images/premium.svg'),
            'image_alt' => esc_attr_x('Upgrade', 'admin-text', 'site-reviews'),
            'upgrade_text' => esc_html_x('Upgrade Now', 'admin-text', 'site-reviews'),
            'upgrade_url' => glsr_premium_url('site-reviews-premium'),
        ];
    }

    protected function styleConfig(): array
    {
        return [
            'style_preset' => [
                'label' => esc_html_x('Style', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'options' => [
                    '1' => esc_html_x('Style 1', 'admin-text', 'site-reviews'),
                    '2' => esc_html_x('Style 2', 'admin-text', 'site-reviews'),
                    '3' => esc_html_x('Style 3', 'admin-text', 'site-reviews'),
                ],
                'placeholder' => esc_html_x('Default', 'admin-text', 'site-reviews'),
                'prefix_class' => 'is-style-',
                'type' => Controls_Manager::SELECT,
            ],
            'style_align' => [
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
                'selectors' => [
                    '{{WRAPPER}} .glsr-summary-text' => 'text-align: {{VALUE}};',
                ],
                'type' => Controls_Manager::CHOOSE,
            ],
            'style_max_width' => [
                'default' => [
                    'unit' => '%',
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-max-w: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['%', 'px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_separator' => [
                'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
                'separator' => 'before',
                'type' => Controls_Manager::HEADING,
            ],
            'style_rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme])' => '--glsr-summary-star-bg: {{VALUE}}',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'style_rating_size' => [
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-summary-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_typography' => [
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'selector' => '{{WRAPPER}} .glsr-summary-rating',
                'type' => Group_Control_Typography::get_type(),
            ],
            'style_text_separator' => [
                'label' => esc_html_x('Text', 'admin-text', 'site-reviews'),
                'separator' => 'before',
                'type' => Controls_Manager::HEADING,
            ],
            'style_text_typography' => [
                'type' => 'typography',
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'selector' => '{{WRAPPER}} .glsr-summary-text',
            ],
            'style_bar_separator' => [
                'label' => esc_html_x('Bars', 'admin-text', 'site-reviews'),
                'separator' => 'before',
                'type' => Controls_Manager::HEADING,
            ],
            'style_bar_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-bar-bg: {{VALUE}}',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'style_bar_gap' => [
                'is_responsive' => true,
                'label' => esc_html_x('Gap', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-bar-spacing: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_bar_size' => [
                'is_responsive' => true,
                'label' => esc_html_x('Percent Bar Height', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-bar-size: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_bar_typography' => [
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'selector' => '{{WRAPPER}} .glsr-summary-percentages',
                'type' => Group_Control_Typography::get_type(),
            ],
        ];
    }
}
