<?php

namespace GeminiLabs\SiteReviews\Integrations\JetWooBuilder;

use Elementor\Widget_Base;
use GeminiLabs\SiteReviews\Controllers\AbstractController;

class Controller extends AbstractController
{
    /**
     * @filter jet-woo-builder/template-functions/product-rating
     */
    public function filterProductRatingHtml(string $html): string
    {
        $pattern = '|(<span class="product-rating__stars">)(.*)(</span>)|is';
        $html = preg_replace($pattern, '$2', $html);
        return $html;
    }

    /**
     * @action elementor/widget/jet-woo-products/skins_init
     * @action elementor/widget/jet-woo-products-list/skins_init
     */
    public function modifyWidgetControls(Widget_Base $widget): void
    {
        $widget->get_controls();
        $widget->remove_control('tab_rating_all');
        $widget->remove_control('tab_rating_rated');
        $widget->remove_control('tab_rating_empty');
        $widget->remove_control('rating_color_rated');
        $widget->remove_control('rating_color_empty');
        $widget->update_responsive_control('rating_font_size', [
            'selectors' => [
                '{{WRAPPER}} .jet-woo-product-rating .glsr-star' => '--glsr-review-star: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $widget->update_control('rating_color_all', [
            'selectors' => [
                '{{WRAPPER}} .jet-woo-product-rating .glsr-star' => 'background: {{VALUE}} !important;',
            ],
        ]);
        if ('jet-woo-products' === $widget->get_name()) {
            $widget->update_responsive_control('rating_alignment', [
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
                    '{{WRAPPER}} .jet-woo-product-rating .glsr-stars' => 'justify-content: {{VALUE}};',
                ],
            ]);
        }
    }
}
