<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Elements;

use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class BricksSiteReviews extends BricksElement
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
            'separator_review' => [
                'group' => 'design',
                'label' => esc_html_x('Review', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'type' => 'separator',
            ],
            'style_text_align' => [
                'exclude' => ['auto', 'justify'],
                'group' => 'design',
                'inline' => true,
                'label' => esc_html_x('Text Align', 'admin-text', 'site-reviews'),
                'rerender' => true,
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'text-align',
            ],
            'style_heading' => [
                'css' => [[
                    'selector' => '.glsr:not([data-theme]) h2, .glsr:not([data-theme]) h3, .glsr:not([data-theme]) h4',
                    'property' => 'font',
                ]],
                'group' => 'design',
                'label' => esc_html_x('Heading', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_rating_color' => [
                'css' => [[
                    'selector' => '.glsr:not([data-theme]) .glsr-review',
                    'property' => '--glsr-review-star-bg',
                ]],
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
        return SiteReviewsShortcode::class;
    }
}
