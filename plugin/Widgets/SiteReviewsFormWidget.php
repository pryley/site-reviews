<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormWidget extends Widget
{
    protected function widgetConfig(): array
    {
        return [
            'assigned_posts' => [
                'description' => esc_html_x('Enter "post_id" to use the Post ID of the current page.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Assign Reviews to Pages', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Assign Reviews to Categories', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter the Term ID or slug of a category.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_users' => [
                'description' => esc_html_x('Enter "user_id" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Assign Reviews to Users', 'admin-text', 'site-reviews'),
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
        return glsr(SiteReviewsFormShortcode::class);
    }
}
