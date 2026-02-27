<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada\Elements;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class FusionSiteReview extends FusionElement
{
    public function elementIcon(): string
    {
        return 'fusion-glsr-review';
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
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
