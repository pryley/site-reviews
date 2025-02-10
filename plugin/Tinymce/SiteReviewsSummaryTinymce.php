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
                'label' => esc_html_x('Assigned Pages', 'admin-text', 'site-reviews'),
                'name' => 'assigned_posts',
                'tooltip' => sprintf(esc_html_x('Limit reviews to those assigned to a Post ID. You may also enter %s to use the Post ID of the current page.', 'admin-text', 'site-reviews'), '"post_id"'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Assigned Categories', 'admin-text', 'site-reviews'),
                'name' => 'assigned_terms',
                'tooltip' => esc_html_x('Limit reviews to those assigned to a category. You may enter a Term ID or slug.', 'admin-text', 'site-reviews'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Assigned Users', 'admin-text', 'site-reviews'),
                'name' => 'assigned_users',
                'tooltip' => sprintf(esc_html_x('Limit reviews to those assigned to a User ID. You may also enter %s to use the ID of the logged-in user.', 'admin-text', 'site-reviews'), '"user_id"'),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Terms Accepted', 'admin-text', 'site-reviews'),
                'name' => 'terms',
                'options' => $this->shortcode->options('terms'),
                'tooltip' => esc_html_x('Limit Reviews by terms accepted', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => esc_html_x('Review Type', 'admin-text', 'site-reviews'),
                'name' => 'type',
                'options' => $this->shortcode->options('type'),
                'tooltip' => esc_html_x('Limit Reviews by review type', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'name' => 'rating',
                'options' => glsr(Rating::class)->optionsArray(),
                'tooltip' => esc_html_x('The minimum rating to display (default: 1 star)?', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
            ],
            [
                'label' => esc_html_x('Rating Field Name', 'admin-text', 'site-reviews'),
                'name' => 'rating_field',
                'tooltip' => sprintf(esc_html_x('Use the %s addon to add custom rating fields.', 'admin-text', 'site-reviews'),
                    sprintf('"%s"', _x('Review Forms', 'addon name (admin-text)', 'site-reviews'))
                ),
                'type' => 'textbox',
            ],
            [
                'label' => esc_html_x('Schema', 'admin-text', 'site-reviews'),
                'name' => 'schema',
                'options' => [
                    '' => esc_html_x('Disabled', 'admin-text', 'site-reviews'),
                    'true' => esc_html_x('Enabled', 'admin-text', 'site-reviews'),
                ],
                'tooltip' => esc_html_x('Rich snippets are disabled by default.', 'admin-text', 'site-reviews'),
                'type' => 'listbox',
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
        return glsr(SiteReviewsSummaryShortcode::class);
    }
}
