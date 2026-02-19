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
            'style_rating_color' => [
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'selectors' => [
                    '.glsr-elementor-{{ID}} .glsr:not([data-theme])' => '--glsr-review-star-bg: {{VALUE}};',
                ],
                'type' => Controls_Manager::COLOR,
            ],
        ];
    }
}
