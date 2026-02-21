<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Elements;

use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class BricksSiteReview extends BricksElement
{
    public function designConfig(): array
    {
        return [
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
                    'property' => 'font',
                    'selector' => '.glsr:not([data-theme]) h2, .glsr:not([data-theme]) h3, .glsr:not([data-theme]) h4',
                ]],
                'group' => 'design',
                'label' => esc_html_x('Heading', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'typography',
            ],
            'style_rating_color' => [
                'css' => [[
                    'selector' => '.glsr:not([data-theme])',
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
        return SiteReviewShortcode::class;
    }
}
