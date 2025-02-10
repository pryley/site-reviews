<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class SiteReviewsFormTinymce extends TinymceGenerator
{
    public function fields(): array
    {
        return [
            [
                'label' => esc_html_x('Assign Pages', 'admin-text', 'site-reviews'),
                'name' => 'assigned_posts',
                'tooltip' => sprintf(esc_html_x('Automatically assign reviews to a Post ID. You may also enter %s to use the Post ID of the current page.', 'admin-text', 'site-reviews'), '"post_id"'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Assign Categories', 'admin-text', 'site-reviews'),
                'name' => 'assigned_terms',
                'tooltip' => esc_html_x('Automatically assign reviews to a category. You may enter a Term ID or slug.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Assign Users', 'admin-text', 'site-reviews'),
                'name' => 'assigned_users',
                'tooltip' => sprintf(esc_html_x('Automatically assign reviews to a User ID. You may also enter %s to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), '"user_id"'),
                'type' => 'textbox',
            ],
            [
                'columns' => 2,
                'items' => $this->hideOptions(),
                'label' => esc_html_x('Hide', 'admin-text', 'site-reviews'),
                'layout' => 'grid',
                'spacing' => 5,
                'type' => 'container',
            ],
            [
                'label' => esc_html_x('Reviews ID', 'admin-text', 'site-reviews'),
                'name' => 'reviews_id',
                'tooltip' => esc_html_x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'name' => 'id',
                'tooltip' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'name' => 'class',
                'tooltip' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
        ];
    }

    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsFormShortcode::class);
    }
}
