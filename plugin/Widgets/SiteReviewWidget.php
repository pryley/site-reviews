<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewWidget extends Widget
{
    protected function widgetConfig(): array
    {
        return [
            'post_id' => [
                'description' => esc_html_x('Enter the Post ID of the review you want to display.', 'admin-text', 'site-reviews'),
                'label' => esc_attr_x('Review Post ID', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'hide' => [
                'options' => $this->shortcode->options('hide'),
                'type' => 'checkbox',
            ],
        ];
    }

    protected function widgetShortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }
}
