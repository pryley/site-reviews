<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Widgets;

use Elementor\Controls_Manager;
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
        ];
    }
}
