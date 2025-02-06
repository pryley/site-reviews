<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class SiteReviewsTinymce extends TinymceGenerator
{
    public function fields(): array
    {
        return [
            [
                'html' => sprintf('<p class="strong">%s</p>', _x('All settings are optional.', 'admin-text', 'site-reviews')),
                'minWidth' => 320,
                'type' => 'container',
            ],
            [
                'label' => esc_html_x('Assigned Pages', 'admin-text', 'site-reviews'),
                'name' => 'assigned_posts',
                'tooltip' => sprintf(_x('Limit reviews to those assigned to a Post ID. You may also enter "%s" to use the Post ID of the current page.', 'admin-text', 'site-reviews'), 'post_id'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Assigned Categories', 'admin-text', 'site-reviews'),
                'name' => 'assigned_terms',
                'tooltip' => esc_html_x('Limit reviews to those assigned to a category. You may enter a Term ID or slug.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Assigned Users', 'admin-text', 'site-reviews'),
                'name' => 'assigned_users',
                'tooltip' => sprintf(_x('Limit reviews to those assigned to a User ID. You may also enter "%s" to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), 'user_id'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Accepted Terms', 'admin-text', 'site-reviews'),
                'name' => 'terms',
                'options' => $this->shortcode->options('terms'),
                'tooltip' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => esc_html_x('Review Type', 'admin-text', 'site-reviews'),
                'name' => 'type',
                'options' => $this->shortcode->options('type'),
                'tooltip' => esc_html_x('Limit Reviews by Type', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => _x('Rating', 'admin-text', 'site-reviews'),
                'name' => 'rating',
                'options' => glsr(Rating::class)->optionsArray(),
                'tooltip' => _x('What is the minimum rating to display (default: 1 star)?', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => _x('Display', 'admin-text', 'site-reviews'),
                'maxLength' => 5,
                'name' => 'display',
                'size' => 3,
                'text' => '10',
                'tooltip' => _x('How many reviews would you like to display (default: 10)?', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Pagination', 'admin-text', 'site-reviews'),
                'name' => 'pagination',
                'options' => $this->shortcode->options('pagination'),
                'tooltip' => _x('When using pagination this shortcode can only be used once on a page. (default: disable)', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => _x('Schema', 'admin-text', 'site-reviews'),
                'name' => 'schema',
                'options' => $this->shortcode->options('schema'),
                'tooltip' => _x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'columns' => 2,
                'items' => $this->hideOptions(),
                'label' => _x('Hide', 'admin-text', 'site-reviews'),
                'layout' => 'grid',
                'spacing' => 5,
                'type' => 'container',
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
        return glsr(SiteReviewsShortcode::class);
    }
}
