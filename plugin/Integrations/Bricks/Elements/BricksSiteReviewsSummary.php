<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Elements;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class BricksSiteReviewsSummary extends BricksElement
{
    public function designConfig(): array
    {
        $config = [
            'style_preset' => [
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Style', 'admin-text', 'site-reviews'),
                'options' => [
                    'default' => esc_html_x('Default', 'admin-text', 'site-reviews'),
                    '1' => esc_html_x('Style 1', 'admin-text', 'site-reviews'),
                    '2' => esc_html_x('Style 2', 'admin-text', 'site-reviews'),
                    '3' => esc_html_x('Style 3', 'admin-text', 'site-reviews'),
                ],
                'placeholder' => esc_html_x('Default', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'select',
            ],
            'style_align' => [
                'css' => [
                    [
                        'selector' => '.glsr-summary',
                        'property' => 'text-align',
                    ],
                ],
                'exclude' => ['auto', 'justify'],
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Alignment', 'admin-text', 'site-reviews'),
                'rerender' => true,
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'text-align',
            ],
            'style_max_width' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-max-w',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Max Width', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_rating_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_rating_color' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-summary-star-bg',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star',
                        'property' => 'background',
                        'value' => 'var(--glsr-summary-star-bg)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star',
                        'property' => 'mask-size',
                        'value' => '100%',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star-empty',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-empty)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star-full',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-full)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star-half',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-half)',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'style_rating_size' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-summary-star',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Star Size', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_rating_typography' => [
                'css' => [
                    [
                        'selector' => '.glsr-summary-rating',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_text_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Text', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_text_typography' => [
                'css' => [
                    [
                        'selector' => '.glsr-summary-text',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_bar_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Bars', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_bar_color' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-bar-bg',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'style_bar_gap' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-bar-spacing',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Gap', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_bar_size' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-bar-size',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Size', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_bar_typography' => [
                'css' => [
                    [
                        'selector' => '.glsr-summary-percentages',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
        ];
        if ($placeholder = $config['style_preset']['options'][$this->theme_styles['style_preset'] ?? ''] ?? '') {
            $config['style_preset']['placeholder'] = $placeholder;
        }
        return $config;
    }

    public function elementConfig(): array
    {
        $controls = parent::elementConfig();
        $controls = Arr::insertBefore('text', $controls, [
            'text_notice' => [
                'content' => esc_html_x('The recommended way to change these values is to use the Site Reviews → Settings → Strings page.', 'admin-text', 'site-reviews'),
                'group' => 'text',
                'tab' => 'content',
                'type' => 'info',
            ],
        ]);
        return $controls;
    }

    public function render()
    {
        if (!$this->shortcodeInstance()->hasVisibleFields($this->settings)) {
            $this->render_element_placeholder([
                'title' => esc_html_x('You have hidden all of the fields.', 'admin-text', 'site-reviews'),
            ]);
            return;
        }
        parent::render();
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
