<?php

namespace GeminiLabs\SiteReviews\Widgets;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormWidget extends Widget
{
    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsFormShortcode::class);
    }

    protected function widgetConfig(): array
    {
        return [
            'assigned_posts' => [
                'label' => esc_html_x('Assign New Reviews to Pages', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter "post_id" to use the Post ID of the current page.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_users' => [
                'label' => esc_html_x('Assign New Reviews to Users', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter "user_id" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'),
                'type' => 'text',
            ],
            'assigned_terms' => [
                'label' => esc_html_x('Assign New Reviews to Categories', 'admin-text', 'site-reviews'),
                'options' => $this->fieldAssignedTermsOptions(),
                'type' => 'select',
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
