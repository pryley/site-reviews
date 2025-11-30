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
                'label' => esc_html__('Align', 'bricks'),
                'rerender' => true,
                'tab' => 'style',
                'themeStyle' => true,
                'type' => 'align-items',
            ],
        ];
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
