<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FusionLatestReviews extends FusionElement
{
    public static function elementParameters(): array
    {
        return [
            'assigned_posts' => [
                'default' => '',
                'heading' => esc_attr_x('Limit Reviews to an Assigned Page', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_posts',
                'type' => 'multiple_select',
                'placeholder_text' => esc_attr_x('Select or Leave Blank', 'admin-text', 'site-reviews'),
                'value' => [
                    'custom' => esc_attr_x('Specific Post ID', 'admin-text', 'site-reviews'),
                    'post_id' => esc_attr_x('The Current Page', 'admin-text', 'site-reviews'),
                    'parent_id' => esc_attr_x('The Parent Page', 'admin-text', 'site-reviews'),
                ],
            ],
            'assigned_posts_custom' => [
                'heading' => esc_attr_x('Assigned Post IDs', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Separate values with a comma.', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_posts_custom',
                'type' => 'textfield',
                'value' => '',
                'dependency' => [
                    [
                        'element' => 'assigned_posts',
                        'value' => 'custom',
                        'operator' => 'contains',
                    ],
                ],
            ],
            'assigned_terms' => static::optionAssignedTerms(esc_attr_x('Limit Reviews to an Assigned Category', 'admin-text', 'site-reviews')),
            'assigned_users' => [
                'default' => '',
                'heading' => esc_attr_x('Limit Reviews to an Assigned User', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_users',
                'placeholder_text' => esc_attr_x('Select or Leave Blank', 'admin-text', 'site-reviews'),
                'type' => 'multiple_select',
                'value' => [
                    'custom' => esc_attr_x('Specific User ID', 'admin-text', 'site-reviews'),
                    'user_id' => esc_attr_x('The Logged-in user', 'admin-text', 'site-reviews'),
                    'author_id' => esc_attr_x('The Page author', 'admin-text', 'site-reviews'),
                    'profile_id' => esc_attr_x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'),
                ],
            ],
            'assigned_users_custom' => [
                'heading' => esc_attr_x('Assigned User IDs', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Separate values with a comma.', 'admin-text', 'site-reviews'),
                'param_name' => 'assigned_users_custom',
                'type' => 'textfield',
                'value' => '',
                'dependency' => [
                    [
                        'element' => 'assigned_users',
                        'value' => 'custom',
                        'operator' => 'contains',
                    ],
                ],
            ],
            'terms' => [
                'default' => '',
                'heading' => esc_attr_x('Limit Reviews to Terms?', 'admin-text', 'site-reviews'),
                'param_name' => 'terms',
                'type' => 'select',
                'value' => [
                    '' => esc_attr_x('No', 'admin-text', 'site-reviews'),
                    'true' => esc_attr_x('Terms were accepted', 'admin-text', 'site-reviews'),
                    'false' => esc_attr_x('Terms were not accepted', 'admin-text', 'site-reviews'),
                ],
            ],
            'type' => static::optionReviewTypes(),
            'pagination' => [
                'default' => '',
                'heading' => esc_attr_x('Pagination Type ', 'admin-text', 'site-reviews'),
                'param_name' => 'pagination',
                'type' => 'select',
                'value' => [
                    '' => esc_attr_x('No Pagination', 'admin-text', 'site-reviews'),
                    'loadmore' => esc_attr_x('Load More Button', 'admin-text', 'site-reviews'),
                    'ajax' => esc_attr_x('Pagination (AJAX)', 'admin-text', 'site-reviews'),
                    'true' => esc_attr_x('Pagination (with page reload)', 'admin-text', 'site-reviews'),
                ],
            ],
            'display' => [
                'default' => 10,
                'heading' => esc_html_x('Reviews Per Page', 'admin-text', 'site-reviews'),
                'max' => 50,
                'min' => 1,
                'param_name' => 'display',
                'type' => 'range',
                'value' => 10,
            ],
            'rating' => [
                'default' => 0,
                'heading' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                'max' => Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)),
                'min' => Cast::toInt(glsr()->constant('MIN_RATING', Rating::class)),
                'param_name' => 'rating',
                'type' => 'range',
                'value' => 0,
            ],
            'schema' => [
                'default' => 0,
                'description' => _x('The schema should only be enabled once per page.', 'admin-text', 'site-reviews'),
                'heading' => esc_html_x('Enable the schema?', 'admin-text', 'site-reviews'),
                'param_name' => 'schema',
                'type' => 'radio_button_set',
                'value' => [
                    0 => esc_html_x('No', 'admin-text', 'site-reviews'),
                    1 => esc_html_x('Yes', 'admin-text', 'site-reviews'),
                ],
            ],
            'hide' => [
                'default' => '',
                'heading' => esc_html_x('Hide Fields', 'admin-text', 'site-reviews'),
                'param_name' => 'hide',
                'placeholder_text' => esc_attr_x('Select Fields to Hide', 'admin-text', 'site-reviews'),
                'type' => 'multiple_select',
                'value' => glsr(SiteReviewsShortcode::class)->getHideOptions(),
            ],
            'class' => [
                'heading' => esc_attr_x('CSS Class', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Add a class to the wrapping HTML element.', 'admin-text', 'site-reviews'),
                'param_name' => 'class',
                'type' => 'textfield',
                'value' => '',
            ],
            'id' => [
                'heading' => esc_attr_x('CSS ID', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Add an ID to the wrapping HTML element.', 'admin-text', 'site-reviews'),
                'param_name' => 'id',
                'type' => 'textfield',
                'value' => '',
            ],
        ];
    }

    public static function registerElement(): void
    {
        $parameters = static::elementParameters();
        $parameters = glsr()->filterArray('fusion-builder/controls/site_reviews', $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'name' => esc_attr_x('Latest Reviews', 'admin-text', 'site-reviews'),
            'shortcode' => 'site_reviews',
            'icon' => 'fusiona-af-rating',
            'params' => $parameters,
        ]));
    }
}
