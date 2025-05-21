<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class ElementorSiteReview extends ElementorWidget
{
    public function get_icon(): string
    {
        return 'eicon-glsr-review';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'alignment' => [
                'default' => 'start',
                'is_responsive' => true,
                'label' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review' => 'text-align: {{VALUE}}; justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-review-actions' => 'justify-content: {{VALUE}};',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-review-date' => 'flex: inherit;',
                ],
                'type' => Controls_Manager::CHOOSE,
            ],
            'rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-empty' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-full' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star-half' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 1.25,
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
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-review .glsr-star' => '--glsr-review-star: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
