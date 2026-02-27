<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
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

    protected function styleConfig(): array
    {
        return [
            'style_text_align' => [
                'group' => 'review',
                'label' => esc_html_x('Text Align', 'admin-text', 'site-reviews'),
                'label_block' => false,
                'default' => 'left',
                'options' => [
                    'left' => [
                        'icon' => 'eicon-text-align-left',
                        'title' => esc_html_x('Left', 'admin-text', 'site-reviews'),
                    ],
                    'center' => [
                        'icon' => 'eicon-text-align-center',
                        'title' => esc_html_x('Center', 'admin-text', 'site-reviews'),
                    ],
                    'right' => [
                        'icon' => 'eicon-text-align-right',
                        'title' => esc_html_x('Right', 'admin-text', 'site-reviews'),
                    ],
                ],
                'prefix_class' => 'has-text-align-',
                'type' => Controls_Manager::CHOOSE,
            ],
            'style_rating_color' => [
                'group' => 'review',
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '.glsr-elementor-{{ID}} .glsr:not([data-theme])' => '--glsr-review-star-bg: {{VALUE}};',
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
