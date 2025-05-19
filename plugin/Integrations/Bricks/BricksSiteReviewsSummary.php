<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class BricksSiteReviewsSummary extends BricksElement
{
    public function designConfig(): array
    {
        $config = [
            'stylePreset' => [
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
            'styleAlign' => [
                'css' => [
                    [
                        'required' => 'left',
                        'selector' => '.glsr-summary',
                        'property' => '--glsr-summary-align',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'left',
                        'selector' => 'div.is-style-1 .glsr-summary-rating, div.is-style-3 .glsr-summary-rating',
                        'property' => 'justify-content',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'center',
                        'selector' => '.glsr-summary',
                        'property' => '--glsr-summary-align',
                    ],
                    [
                        'required' => 'center',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary',
                        'property' => 'grid-template-columns',
                        'value' => '1fr auto auto 1fr',
                    ],
                    [
                        'required' => 'center',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-rating',
                        'property' => 'grid-column-start',
                        'value' => '2',
                    ],
                    [
                        'required' => 'center',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-stars, div:is(.is-style-1, .is-style-3) .glsr-summary-text',
                        'property' => 'grid-column-start',
                        'value' => '3',
                    ],
                    [
                        'required' => 'center',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-stars, div:is(.is-style-1, .is-style-3) .glsr-summary-text',
                        'property' => 'text-align',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'center',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-percentages',
                        'property' => 'grid-column-end',
                        'value' => 'span 4',
                    ],
                    [
                        'required' => 'right',
                        'selector' => '.glsr-summary',
                        'property' => '--glsr-summary-align',
                        'value' => 'end',
                    ],
                    [
                        'required' => 'right',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary',
                        'property' => 'grid-template-columns',
                        'value' => '1fr auto',
                    ],
                    [
                        'required' => 'right',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-rating',
                        'property' => 'justify-content',
                        'value' => 'end',
                    ],
                    [
                        'required' => 'right',
                        'selector' => 'div:is(.is-style-1, .is-style-3) .glsr-summary-text',
                        'property' => 'text-align',
                        'value' => 'start',
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
            'styleMaxWidth' => [
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
            'separatorRating' => [
                'group' => 'design',
                'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleRatingColor' => [
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
            'styleRatingSize' => [
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
            'styleRatingTypography' => [
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
            'separatorText' => [
                'group' => 'design',
                'label' => esc_html_x('Text', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleTextTypography' => [
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
            'separatorBar' => [
                'group' => 'design',
                'label' => esc_html_x('Bars', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'styleBarColor' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-bar-bg',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'styleBarSpacing' => [
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
            'styleBarSize' => [
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
            'styleBarTypography' => [
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
        if ($placeholder = $config['stylePreset']['options'][$this->theme_styles['stylePreset'] ?? ''] ?? '') {
            $config['stylePreset']['placeholder'] = $placeholder;
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

    public function styledClasses(array $classes = []): array
    {
        if ($align = $this->styledSetting('styleAlign')) {
            $classes[] = "items-justified-{$align}";
        }
        return $classes;
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsSummaryShortcode::class;
    }
}
