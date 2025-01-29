<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class VcSiteReviewsForm extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-form.svg');
    }

    public static function vcShortcodeSettings(): array
    {
        return [
            'assigned_posts' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Assign New Reviews to Pages', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_posts',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'assigned_terms' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Assign New Reviews to Categories', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_terms',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'assigned_users' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Assign New Reviews to Users', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_users',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'hide' => [
                'type' => 'checkbox',
                'heading' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'param_name' => 'hide',
                'value' => array_flip(static::vcShortcode()->getHideOptions()),
            ],
            'reviews_id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Reviews ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Enter the Custom ID of a reviews block, shortcode, or widget where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'param_name' => 'reviews_id',
                'group' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            ],
            'id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                'param_name' => 'id',
                'group' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            ],
            'class' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                'param_name' => 'class',
                'group' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
            ],
        ];
    }
}
