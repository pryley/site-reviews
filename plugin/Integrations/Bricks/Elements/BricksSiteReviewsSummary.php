<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks\Elements;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Bricks\BricksElement;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class BricksSiteReviewsSummary extends BricksElement
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
            'separator_summary_rating' => [
                'group' => 'design',
                'label' => esc_html_x('Rating', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_rating_color' => [
                'css' => [[
                    'selector' => '.glsr:not([data-theme])',
                    'property' => '--glsr-summary-star-bg',
                ]],
                'group' => 'design',
                'label' => esc_html_x('Rating Color', 'admin-text', 'site-reviews'),
                'rerender' => true, // because we have to set a CSS class
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'color',
            ],
            'separator_summary_bars' => [
                'group' => 'design',
                'label' => esc_html_x('Percent Bars', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'separator',
            ],
            'style_bar_color' => [
                'css' => [[
                    'selector' => '.glsr',
                    'property' => '--glsr-bar-bg',
                ]],
                'group' => 'design',
                'label' => esc_html_x('Bar Color', 'admin-text', 'site-reviews'),
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'color',
            ],
        ];
    }

    public function elementConfig(): array
    {
        $controls = parent::elementConfig();
        $controls = Arr::insertBefore('text', $controls, [
            'notice_text' => [
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
