<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryWidget extends Widget
{
    protected function widgetConfig(): array
    {
        return [
            'assigned_posts' => [
                'description' => esc_html_x('Enter "post_id" to use the Post ID of the current page.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_terms' => [
                'description' => esc_html_x('Enter the Term ID or slug of a category.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Limit Reviews by Assigned Categories', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_users' => [
                'description' => esc_html_x('Enter "user_id" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'),
                'label' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'terms' => [
                'label' => esc_html_x('Limit Reviews by terms accepted', 'admin-text', 'site-reviews'),
                'options' => $this->shortcode->options('terms', [
                    'placeholder' => _x('— Select —', 'admin-text', 'site-reviews'),
                ]),
                'type' => 'select',
            ],
            'type' => [
                'label' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
                'options' => $this->shortcode->options('type'),
                'type' => 'select',
                'value' => 'local',
            ],
            'rating' => [
                'label' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Rating::max(),
                'min' => max(1, Rating::min()),
                'type' => 'number',
                'value' => max(1, Rating::min()),
            ],
            'hide' => [
                'options' => $this->shortcode->options('hide'),
                'type' => 'checkbox',
            ],
        ];
    }

    protected function widgetShortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }
}
