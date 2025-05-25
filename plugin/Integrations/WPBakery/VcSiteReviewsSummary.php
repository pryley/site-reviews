<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Modules\Rating;
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

    protected static function vcStyleConfig(): array
    {
        return [
            // 'style_preset' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Style', 'admin-text', 'site-reviews'),
            //     'options' => [
            //         '1' => esc_html_x('Style 1', 'admin-text', 'site-reviews'),
            //         '2' => esc_html_x('Style 2', 'admin-text', 'site-reviews'),
            //         '3' => esc_html_x('Style 3', 'admin-text', 'site-reviews'),
            //     ],
            //     'placeholder' => esc_html_x('Default', 'admin-text', 'site-reviews'),
            //     'prefix_class' => 'is-style-',
            //     'std' => '',
            //     'type' => 'dropdown',
            // ],
            // 'style_align' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
            //     'options' => [
            //         'left' => esc_html_x('Start', 'admin-text', 'site-reviews'),
            //         'center' => esc_html_x('Center', 'admin-text', 'site-reviews'),
            //         'right' => esc_html_x('End', 'admin-text', 'site-reviews'),
            //     ],
            //     'std' => 'left',
            //     'type' => 'dropdown',
            // ],
            // 'style_max_width' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
            //     'type' => 'textfield',
            // ],
            // 'style_rating_color' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
            //     'type' => 'colorpicker',
            // ],
            // 'style_rating_size' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
            //     'type' => 'textfield',
            // ],
            // 'style_bar_color' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
            //     'type' => 'colorpicker',
            // ],
            // 'style_bar_gap' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Bar Gap', 'admin-text', 'site-reviews'),
            //     'type' => 'textfield',
            // ],
            // 'style_bar_size' => [
            //     'group' => 'design',
            //     'heading' => esc_html_x('Bar Size', 'admin-text', 'site-reviews'),
            //     'type' => 'textfield',
            // ],
        ];
    }
}
