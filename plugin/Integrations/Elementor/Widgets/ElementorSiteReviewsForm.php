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
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '.glsr-elementor-{{ID}} .glsr:not([data-theme])' => '--glsr-form-star-bg: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }
}
