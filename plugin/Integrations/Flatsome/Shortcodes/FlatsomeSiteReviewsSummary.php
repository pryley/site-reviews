<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class FlatsomeSiteReviewsSummary extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-summary.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_align' => [
                'default' => 'left',
                'group' => 'design',
                'heading' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'type' => 'radio-buttons',
                'on_change' => array(
                    'class' => 'items-justified-{{ value }}',
                    'recompile' => false,
                ),
                'options' => [
                    'left' => [
                        'icon' => 'dashicons-align-left',
                        'title' => _x('Left', 'admin-text', 'site-reviews'),
                    ],
                    'center' => [
                        'icon' => 'dashicons-align-center',
                        'title' => _x('Center', 'admin-text', 'site-reviews'),
                    ],
                    'right' => [
                        'icon' => 'dashicons-align-right',
                        'title' => _x('Right', 'admin-text', 'site-reviews'),
                    ],
                ],
            ],
            'style_max_width' => [
                'description' => esc_attr_x('Enter a valid CSS unit.', 'admin-text', 'site-reviews'),
                'group' => 'design',
                'heading' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'default' => '48ch',
                'min' => 0,
                'on_change' => [
                    'style' => '--glsr-max-w:{{ value }}'
                ],
                'type' => 'scrubfield',
            ],
            'style_rating_color' => [
                'alpha' => true,
                'format' => 'rgb',
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'helpers' => require(get_template_directory().'/inc/builder/shortcodes/helpers/colors.php'),
                'position' => 'bottom right',
                'type' => 'colorpicker',
            ],
            'style_bar_color' => [
                'alpha' => true,
                'format' => 'rgb',
                'group' => 'design',
                'heading' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'helpers' => require(get_template_directory().'/inc/builder/shortcodes/helpers/colors.php'),
                'on_change' => [
                    'style' => '--glsr-bar-bg:{{ value }}',
                ],
                'position' => 'bottom right',
                'type' => 'colorpicker',
            ],
        ];
    }

    protected function styles(): array
    {
        return [
            'site-reviews-summary-style' => glsr()->url('assets/blocks/site_reviews_summary/style-index.css'),
        ];
    }
}
