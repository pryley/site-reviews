<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class VcSiteReviewsForm extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-form.svg');
    }

    protected static function vcStyleConfig(): array
    {
        return [
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpicker',
            ],
            'style_align' => [
                'group' => 'design',
                'heading' => esc_html_x('Button Align', 'admin-text', 'site-reviews'),
                'type' => 'dropdown',
                'value' => [
                    esc_html_x('Left', 'admin-text', 'site-reviews') => 'left',
                    esc_html_x('Center', 'admin-text', 'site-reviews') => 'center',
                    esc_html_x('Right', 'admin-text', 'site-reviews') => 'right',
                ],
            ],
        ];
    }
}
