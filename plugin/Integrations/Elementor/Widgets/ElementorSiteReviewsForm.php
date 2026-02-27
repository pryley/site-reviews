<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

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
            'style_rating_color' => [
                'group' => 'fields',
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '.glsr-elementor-{{ID}} .glsr:not([data-theme])' => '--glsr-form-star-bg: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
            'style_button_align' => [
                'default' => 'stretch',
                'group' => 'button',
                'label' => esc_html_x('Position', 'admin-text', 'site-reviews'),
                'options' => [
                    'start' => [
                        'title' => esc_html_x('Left', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html_x('Center', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'end' => [
                        'title' => esc_html_x('Right', 'admin-text', 'site-reviews'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'elementor-button-align-',
                'type' => Controls_Manager::CHOOSE,
            ],
        ];
    }
}
