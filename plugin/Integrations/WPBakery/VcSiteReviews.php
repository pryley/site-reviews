<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class VcSiteReviews extends VcShortcode
{
    public static function vcShortcodeClass(): string
    {
        return SiteReviewsShortcode::class;
    }

    public static function vcShortcodeIcon(): string
    {
        return glsr()->url('assets/images/icons/wpbakery/icon-reviews.svg');
    }

    public static function vcShortcodeSettings(): array
    {
        return [
            'assigned_posts' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_posts',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'assigned_terms' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Limit Reviews by Assigned Categories', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_terms',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'assigned_users' => [
                'type' => 'autocomplete',
                'heading' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_users',
                'settings' => [
                    'multiple' => true,
                    'sortable' => true,
                ],
            ],
            'type' => static::vcTypeOptions(),
            'terms' => [
                'type' => 'dropdown',
                'heading' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                'param_name' => 'terms',
                'std' => '',
                'value' => [
                    esc_html_x('Select Terms...', 'admin-text', 'site-reviews') => '',
                    esc_html_x('Terms were accepted', 'admin-text', 'site-reviews') => 'true',
                    esc_html_x('Terms were not accepted', 'admin-text', 'site-reviews') => 'false',
                ],
            ],
            'pagination' => [
                'type' => 'dropdown',
                'heading' => esc_html_x('Pagination Type', 'admin-text', 'site-reviews'),
                'param_name' => 'pagination',
                'std' => '',
                'value' => [
                    esc_attr_x('No Pagination', 'admin-text', 'site-reviews') => '',
                    esc_attr_x('Load More Button', 'admin-text', 'site-reviews') => 'loadmore',
                    esc_attr_x('Pagination (AJAX)', 'admin-text', 'site-reviews') => 'ajax',
                    esc_attr_x('Pagination (with page reload)', 'admin-text', 'site-reviews') => 'true',
                ],
            ],
            'display' => [
                'type' => 'glsr_type_range',
                'heading' => esc_html_x('Reviews Per Page', 'admin-text', 'site-reviews'),
                'max' => 50,
                'min' => 1,
                'param_name' => 'display',
                'std' => 10,
            ],
            'rating' => [
                'type' => 'glsr_type_range',
                'heading' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Rating::max(),
                'min' => Rating::min(),
                'param_name' => 'rating',
                'std' => Rating::min(),
            ],
            'schema' => [
                'type' => 'checkbox',
                'heading' => esc_html_x('Enable the schema?', 'admin-text', 'site-reviews'),
                'description' => esc_html_x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews'),
                'param_name' => 'schema',
                'value' => [
                    esc_html_x('Yes', 'admin-text', 'site-reviews') => 'true',
                ],
            ],
            'hide' => [
                'type' => 'checkbox',
                'heading' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'param_name' => 'hide',
                'value' => array_flip(static::vcShortcode()->getHideOptions()),
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
