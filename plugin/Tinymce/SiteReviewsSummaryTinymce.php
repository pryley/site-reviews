<?php

namespace GeminiLabs\SiteReviews\Tinymce;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class SiteReviewsSummaryTinymce extends TinymceGenerator
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
                'label' => _x('Assigned Posts', 'admin-text', 'site-reviews'),
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
                'label' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                'name' => 'terms',
                'options' => $this->shortcode->options('terms', [
                    'placeholder' => _x('— Select —', 'admin-text', 'site-reviews'),
                ]),
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
                'options' => glsr(Rating::class)->optionsArray([], 1),
                'tooltip' => _x('What is the minimum rating to use?', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => esc_html_x('Custom Rating Field Name', 'admin-text', 'site-reviews'),
                'name' => 'rating_field',
                'tooltip' => sprintf(_x('Use the %sReview Forms%s addon to add custom rating fields.', 'admin-text', 'site-reviews'),
                    '<a href="https://niftyplugins.com/plugins/site-reviews-forms/" target="_blank">', '</a>'
                ),
                'type' => 'textbox',
            ],
            [
                'label' => _x('Schema', 'admin-text', 'site-reviews'),
                'name' => 'schema',
                'options' => [
                    'true' => _x('Enable rich snippets', 'admin-text', 'site-reviews'),
                    'false' => _x('Disable rich snippets', 'admin-text', 'site-reviews'),
                ],
                'tooltip' => _x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
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
            [
                'columns' => 2,
                'items' => $this->hideOptions(),
                'label' => _x('Hide', 'admin-text', 'site-reviews'),
                'layout' => 'grid',
                'spacing' => 5,
                'type' => 'container',
            ],
        ];
    }

    public function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsSummaryShortcode::class);
    }
}
