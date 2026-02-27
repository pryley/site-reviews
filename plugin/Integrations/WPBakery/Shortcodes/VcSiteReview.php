<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class VcSiteReview extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-review.svg');
    }

    protected static function vcStyleConfig(): array
    {
        return [
            'style_text_align' => [
                'group' => 'design',
                'heading' => esc_html_x('Text Align', 'admin-text', 'site-reviews'),
                'type' => 'dropdown',
                'value' => [
                    esc_html_x('Left', 'admin-text', 'site-reviews') => 'left',
                    esc_html_x('Center', 'admin-text', 'site-reviews') => 'center',
                    esc_html_x('Right', 'admin-text', 'site-reviews') => 'right',
                ],
            ],
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpicker',
            ],
        ];
    }
}
