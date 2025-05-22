<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class BricksSiteReviewsForm extends BricksElement
{
    public function designConfig(): array
    {
        $config = [
            'style_col_gap' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-form-col-gap',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Column Gap', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_row_gap' => [
                'css' => [
                    [
                        'selector' => '.glsr',
                        'property' => '--glsr-form-row-gap',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Row Gap', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_field_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Fields', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_field_background_color' => [
                'css' => [
                    [
                        'selector' => '.glsr-textarea, .glsr-input',
                        'property' => 'background-color',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Background color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'style_field_border' => [
                'css' => [
                    [
                        'selector' => '.glsr-textarea, .glsr-input',
                        'property' => 'border',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Border', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'border',
            ],
            'style_field_typography' => [
                'css' => [
                    [
                        'selector' => '.glsr-textarea, .glsr-input, .glsr-toggle label, .glsr-toggle-switch',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Typography', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_field_label_typography' => [
                'css' => [
                    [
                        'selector' => 'label.glsr-label',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Labels', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_field_placeholder' => [
                'css' => [
                    [
                        'selector' => '::placeholder',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Placeholder', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_rating_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Rating Field', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_rating_color' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-form-star-bg',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span',
                        'property' => 'background',
                        'value' => 'var(--glsr-form-star-bg)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-empty)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span',
                        'property' => 'mask-size',
                        'value' => '100%',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-field .glsr-star-rating--stars > span:is(.gl-active, .gl-active.gl-selected)',
                        'property' => 'background',
                        'value' => 'var(--glsr-form-star-bg)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-field .glsr-star-rating--stars > span:is(.gl-active, .gl-active.gl-selected)',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-full)',
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
                        'property' => '--glsr-form-star',
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
            'style_rating_gap' => [
                'css' => [
                    [
                        'selector' => '.glsr-field-rating span[data-rating]',
                        'property' => 'column-gap',
                    ],
                ],
                'group' => 'design',
                'hasDynamicData' => false,
                'hasVariables' => true,
                'inline' => true,
                'label' => esc_html_x('Star Spacing', 'admin-text', 'site-reviews'),
                'placeholder' => '',
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'number',
                'units' => true,
            ],
            'style_toggle_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Toggle Field', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_toggle_color' => [
                'css' => [
                    [
                        'selector' => '.glsr-field-toggle',
                        'property' => '--glsr-toggle-bg-1',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Toggle Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'style_button_separator' => [
                'group' => 'design',
                'label' => esc_html_x('Submit Button', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_button_size' => [
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Size', 'admin-text', 'site-reviews'),
                'options' => $this->control_options['buttonSizes'] ?? [],
                'placeholder' => esc_html_x('Default', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'select',
            ],
            'style_button_preset' => [
                'default' => 'primary',
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Style', 'admin-text', 'site-reviews'),
                'options' => $this->control_options['styles'] ?? [],
                'placeholder' => esc_html_x('None', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'select',
            ],
            'style_button_background_color' => [
                'css' => [
                    [
                        'selector' => '.bricks-button',
                        'property' => 'background-color',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Background', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'style_button_border' => [
                'css' => [
                    [
                        'selector' => '.bricks-button',
                        'property' => 'border',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Border', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'border',
            ],
            'style_button_typography' => [
                'css' => [
                    [
                        'selector' => '.bricks-button',
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
        return $config;
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
        return SiteReviewsFormShortcode::class;
    }
}
