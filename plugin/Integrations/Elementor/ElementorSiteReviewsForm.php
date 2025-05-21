<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use Elementor\Controls_Manager;
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
            'spacing' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 0.75,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Field Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 2,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .glsr-review-form' => '--glsr-gap-md: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .elementor-form .elementor-form-fields-wrapper .glsr-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'rating_color' => [
                'global' => [
                    'default' => '',
                ],
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span' => 'background: {{VALUE}} !important;',
                    '{{WRAPPER}} .glsr:not([data-theme]) .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active' => 'background: {{VALUE}} !important;',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'rating_size' => [
                'default' => [
                    'unit' => 'em',
                    'size' => 2,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'range' => [
                    'em' => [
                        'min' => 1,
                        'max' => 3,
                        'step' => 0.125,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} form.glsr-form .glsr-field-rating' => '--glsr-form-star: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .glsr[data-theme] .glsr-field-rating [data-stars]' => 'font-size: initial;',
                    '{{WRAPPER}} .glsr[data-theme] .glsr-field-rating [data-stars] > span' => 'font-size: initial; height: var(--glsr-form-star); width: var(--glsr-form-star);',
                ],
                'size_units' => ['em', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
            'rating_spacing' => [
                'default' => [
                    'unit' => 'px',
                    'size' => 2,
                ],
                'is_responsive' => true,
                'label' => esc_html_x('Star Spacing', 'admin-text', 'site-reviews'),
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} form.glsr-form .glsr-field-rating span[data-rating]' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
                'size_units' => ['px', 'custom'],
                'type' => Controls_Manager::SLIDER,
            ],
        ];
    }
}
