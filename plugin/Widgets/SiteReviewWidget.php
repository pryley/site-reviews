<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewShortcode;

class SiteReviewWidget extends Widget
{
    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewShortcode::class);
    }

    protected function widgetConfig(): array
    {
        return [
            'post_id' => [
                'label' => esc_html_x('Review ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter the Post ID of the review you want to display.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'hide' => [
                'options' => $this->shortcode()->getHideOptions(),
                'type' => 'checkbox',
            ],
            'id' => [
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'class' => [
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
        ];
    }
}
