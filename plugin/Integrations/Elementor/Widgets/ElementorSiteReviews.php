<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class ElementorSiteReviews extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-reviews';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
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
            'style_row_gap' => [
                'is_responsive' => true,
                'label' => esc_html_x('Row Gap', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-review-row-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_heading' => [
                'label' => esc_html_x('Heading', 'admin-text', 'site-reviews'),
                'selector' => '{{WRAPPER}} .glsr:not([data-theme]) h2, {{WRAPPER}} .glsr:not([data-theme]) h3, {{WRAPPER}} .glsr:not([data-theme]) h4',
                'type' => Group_Control_Typography::get_type(),
            ],
            'style_text' => [
                'label' => esc_html_x('Text', 'admin-text', 'site-reviews'),
                'selector' => '{{WRAPPER}} .glsr:not([data-theme])',
                'type' => Group_Control_Typography::get_type(),
            ],
            'style_rating_size' => [
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}}' => '--glsr-review-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'style_rating_color' => [
                'label' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme])' => '--glsr-review-star-bg: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }

    protected function transformControl(string $name, array $args): array
    {
        $control = parent::transformControl($name, $args);
        if ('pagination' === $name) {
            $icons = [
                'ajax' => 'eicon eicon-spinner',
                'loadmore' => 'eicon eicon-button',
                'true' => 'eicon eicon-redo',
            ];
            $control['label'] = _x('Pagination', 'admin-text', 'site-reviews');
            $control['label_block'] = false;
            $control['type'] = Controls_Manager::CHOOSE;
            foreach ($control['options'] as $key => $value) {
                $control['options'][$key] = [
                    'icon' => $icons[$key] ?? $icons['ajax'],
                    'title' => $value,
                ];
            }
        }
        return $control;
    }
}
