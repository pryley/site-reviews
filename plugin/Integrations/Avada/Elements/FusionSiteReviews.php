<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FusionSiteReviews extends FusionElement
{
    public function elementIcon(): string
    {
        return 'fusion-glsr-reviews';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_align' => [
                'back_icons' => true,
                'default' => 'left',
                'grid_layout' => true,
                'group' => 'design',
                'heading' => esc_html_x('Review Align', 'admin-text', 'site-reviews'),
                'icons' => [
                    'left' => '<span class="fusiona-horizontal-flex-start"></span>',
                    'center' => '<span class="fusiona-horizontal-flex-center"></span>',
                    'right' => '<span class="fusiona-horizontal-flex-end"></span>',
                ],
                'type' => 'radio_button_set',
                'value' => [
                    'left' => esc_attr_x('Start', 'admin-text', 'site-reviews'),
                    'center' => esc_attr_x('Center', 'admin-text', 'site-reviews'),
                    'right' => esc_attr_x('End', 'admin-text', 'site-reviews'),
                ],
            ],
            'style_text_align' => [
                'default' => 'left',
                'group' => 'design',
                'heading' => esc_attr_x('Text Align', 'admin-text', 'site-reviews'),
                'type' => 'radio_button_set',
                'value' => [
                    'left' => esc_attr_x('Left', 'admin-text', 'site-reviews'),
                    'center' => esc_attr_x('Center', 'admin-text', 'site-reviews'),
                    'right' => esc_attr_x('Right', 'admin-text', 'site-reviews'),
                ],
            ],
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
        ];
    }
}
