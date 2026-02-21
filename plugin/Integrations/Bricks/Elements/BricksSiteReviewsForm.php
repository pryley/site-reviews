<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Elements;

use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class BricksSiteReviewsForm extends BricksElement
{
    public function designConfig(): array
    {
        return [
            'style_align' => [
                'exclude' => ['stretch', 'auto'],
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Align', 'admin-text', 'site-reviews'),
                'rerender' => true,
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'align-items',
            ],
            'style_rating_color' => [
                'css' => [
                    [
                        'selector' => '.glsr:not([data-theme])',
                        'property' => '--glsr-form-star-bg',
                    ],
                ],
                'group' => 'design',
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'rerender' => true, // because we have to set a CSS class
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'color',
            ],
        ];
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
