<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class BricksSiteReviewsForm extends BricksElement
{
    public function designConfig(): array
    {
        $config = [
            'styleFormColGap' => [
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
            'styleFormRowGap' => [
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
            'separatorField' => [
                'group' => 'design',
                'label' => esc_html_x('Fields', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleFieldBackgroundColor' => [
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
            'styleFieldBorder' => [
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
            'styleFieldTypography' => [
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
            'styleFieldLabelTypography' => [
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
            'styleFieldPlaceholder' => [
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
            'separatorRating' => [
                'group' => 'design',
                'label' => esc_html_x('Rating Field', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleRatingSize' => [
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
            'styleRatingColor' => [
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
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'separatorToggle' => [
                'group' => 'design',
                'label' => esc_html_x('Toggle Field', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleToggleColor' => [
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
            'separatorButton' => [
                'group' => 'design',
                'label' => esc_html_x('Submit Button', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleButtonSize' => [
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Size', 'admin-text', 'site-reviews'),
                'options' => $this->control_options['buttonSizes'] ?? [],
                'placeholder' => esc_html_x('Default', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'select',
            ],
            'styleButtonStyle' => [
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
            'styleButtonBackgroundColor' => [
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
            'styleButtonBorder' => [
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
            'styleButtonTypography' => [
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
