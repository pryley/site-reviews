<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryWidget extends Widget
{
    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }

    protected function widgetConfig(): array
    {
        return [
            'assigned_posts' => [
                'label' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter "post_id" to use the Post ID of the current page.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_users' => [
                'label' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter "user_id" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Limit Reviews by Categories', 'admin-text', 'site-reviews'),
                'options' => $this->fieldAssignedTermsOptions(),
                'type' => 'select',
            ],
            'terms' => [
                'label' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                'options' => [
                    '' => esc_html_x('— Select —', 'admin-text', 'site-reviews'),
                    'true' => esc_html_x('Terms were accepted', 'admin-text', 'site-reviews'),
                    'false' => esc_html_x('Terms were not accepted', 'admin-text', 'site-reviews'),
                ],
                'type' => 'select',
            ],
            'type' => [
                'label' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
                'options' => $this->fieldTypeOptions(),
                'type' => 'select',
                'value' => 'local',
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
