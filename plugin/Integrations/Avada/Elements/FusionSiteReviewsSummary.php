<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FusionSiteReviewsSummary extends FusionElement
{
    public function elementIcon(): string
    {
        return 'fusion-glsr-summary';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_align' => [
                'back_icons' => true,
                'default' => 'left',
                'grid_layout' => true,
                'group' => 'design',
                'heading' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
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
            'style_max_width' => [
                'description' => esc_attr_x('Enter value including any valid CSS unit, e.g. 100%.', 'admin-text', 'site-reviews'),
                'heading' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'type' => 'textfield',
            ],
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
            'style_bar_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpickeralpha',
            ],
        ];
    }
}
