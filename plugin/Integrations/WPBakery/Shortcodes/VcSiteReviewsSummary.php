<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class VcSiteReviewsSummary extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-summary.svg');
    }

    protected static function vcSettingsConfig(): array
    {
        $config = parent::vcSettingsConfig();
        $config['rating']['default'] = '1';
        return $config;
    }

    protected static function vcStyleConfig(): array
    {
        return [
            'style_align' => [
                'group' => 'design',
                'heading' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'type' => 'dropdown',
                'value' => [
                    esc_html_x('Left', 'admin-text', 'site-reviews') => 'left',
                    esc_html_x('Center', 'admin-text', 'site-reviews') => 'center',
                    esc_html_x('Right', 'admin-text', 'site-reviews') => 'right',
                ],
            ],
            'style_max_width' => [
                'group' => 'design',
                'description' => esc_attr_x('Enter a valid CSS unit.', 'admin-text', 'site-reviews'),
                'heading' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'type' => 'textfield',
                'value' => '48ch',
            ],
            'style_rating_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpicker',
            ],
            'style_bar_color' => [
                'group' => 'design',
                'heading' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'type' => 'colorpicker',
            ],
        ];
    }
}
