<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FlatsomeSiteReviews extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-reviews.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
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
            'style_text_align' => [
                'default' => 'left',
                'group' => 'design',
                'heading' => esc_html_x('Text Align', 'admin-text', 'site-reviews'),
                'type' => 'radio-buttons',
                'on_change' => array(
                    'class' => 'has-text-align-{{ value }}',
                    'recompile' => false,
                ),
                'options' => [
                    'left' => [
                        'icon' => 'dashicons-editor-alignleft',
                        'title' => _x('Left', 'admin-text', 'site-reviews'),
                    ],
                    'center' => [
                        'icon' => 'dashicons-editor-aligncenter',
                        'title' => _x('Center', 'admin-text', 'site-reviews'),
                    ],
                    'right' => [
                        'icon' => 'dashicons-editor-alignright',
                        'title' => _x('Right', 'admin-text', 'site-reviews'),
                    ],
                ],
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
        ];
    }

    protected function styles(): array
    {
        return [
            'site-reviews-reviews-style' => glsr()->url('assets/blocks/site_reviews/style-index.css'),
        ];
    }
}
