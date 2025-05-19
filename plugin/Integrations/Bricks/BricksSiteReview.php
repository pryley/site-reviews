<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class BricksSiteReview extends BricksElement
{
    public function designConfig(): array
    {
        $config = [
            'styleReviewAlign' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-review',
                        'property' => 'text-align',
                    ],
                    [
                        'required' => 'left',
                        'selector' => '.glsr:not([data-theme]) .glsr-review',
                        'property' => 'justify-content',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'left',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-rating',
                        'property' => 'flex',
                        'value' => '0',
                    ],
                    [
                        'required' => 'left',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-date',
                        'property' => 'flex',
                        'value' => '1',
                    ],
                    [
                        'required' => 'left',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-date',
                        'property' => 'justify-content',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'center',
                        'selector' => '.glsr:not([data-theme]) .glsr-review',
                        'property' => 'justify-content',
                    ],
                    [
                        'required' => 'center',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-rating, .glsr:not([data-theme]) .glsr-review-date',
                        'property' => 'flex',
                        'value' => 'auto',
                    ],
                    [
                        'required' => 'center',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-rating',
                        'property' => 'justify-content',
                        'value' => 'end',
                    ],
                    [
                        'required' => 'center',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-date',
                        'property' => 'justify-content',
                        'value' => 'start',
                    ],
                    [
                        'required' => 'right',
                        'selector' => '.glsr:not([data-theme]) .glsr-review',
                        'property' => 'justify-content',
                        'value' => 'end',
                    ],
                    [
                        'required' => 'right',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-rating',
                        'property' => 'justify-content',
                        'value' => 'end',
                    ],
                    [
                        'required' => 'right',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-rating',
                        'property' => 'flex',
                        'value' => '1',
                    ],
                    [
                        'required' => 'right',
                        'selector' => '.glsr:not([data-theme]) .glsr-review-date',
                        'property' => 'flex',
                        'value' => '0',
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
            'styleReviewRowGap' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-review-row-gap',
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
            'styleReviewHeading' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme]) h2, .glsr:not([data-theme]) h3, .glsr:not([data-theme]) h4',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Heading', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'styleReviewText' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => 'font',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Text', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'styleReviewStarSize' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-review-star',
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
            'styleReviewStarColor' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star',
                        'property' => 'mask-size',
                        'value' => '100%',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star',
                        'property' => 'background',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star-full',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-full)',
                    ],
                    [
                        'selector' => '.glsr:not([data-theme]) .glsr-star-empty',
                        'property' => 'mask-image',
                        'value' => 'var(--glsr-star-empty)',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Star Color', 'admin-text', 'site-reviews'),
                'tab' => 'content',
                'themeStyle' => true,
                'type' => 'color',
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
        return SiteReviewShortcode::class;
    }
}
