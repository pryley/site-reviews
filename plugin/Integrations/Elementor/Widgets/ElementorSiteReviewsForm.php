<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class ElementorSiteReviewsForm extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-form';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_col_gap' => [
                'is_responsive' => true,
                'label' => esc_html_x('Column Gap', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-form-col-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_row_gap' => [
                'is_responsive' => true,
                'label' => esc_html_x('Row Gap', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-form-row-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_separator' => [
                'label' => esc_html_x('Rating Field', 'admin-text', 'site-reviews'),
                'separator' => 'before',
                'type' => Controls_Manager::HEADING,
            ],
            'style_rating_color' => [
                'label' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme])' => '--glsr-form-star-bg: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'style_rating_size' => [
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-form-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_gap' => [
                'is_responsive' => true,
                'label' => esc_html_x('Star Spacing', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr-field-rating span[data-rating]' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_toggle_separator' => [
                'label' => esc_html_x('Toggle Field', 'admin-text', 'site-reviews'),
                'separator' => 'before',
                'type' => Controls_Manager::HEADING,
            ],
            'style_toggle_color' => [
                'label' => esc_html_x('Toggle Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr-field-toggle' => '--glsr-toggle-bg-1: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }
}
