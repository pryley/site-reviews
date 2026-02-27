<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Shortcodes;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FlatsomeSiteReviewsForm extends FlatsomeShortcode
{
    public function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-form.svg');
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    protected function styleConfig(): array
    {
        return [
            'style_rating_color' => [
                'alpha' => true,
                'format' => 'rgb',
                'group' => 'design',
                'heading' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'helpers' => require(get_template_directory().'/inc/builder/shortcodes/helpers/colors.php'),
                'position' => 'bottom right',
                'type' => 'colorpicker',
            ],
            'style_align' => [
                'default' => 'left',
                'group' => 'design',
                'heading' => esc_html_x('Button Align', 'admin-text', 'site-reviews'),
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
        ];
    }

    protected function styles(): array
    {
        return [
            'site-reviews-form-style' => glsr()->url('assets/blocks/site_reviews_form/style-index.css'),
        ];
    }
}
