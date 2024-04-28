<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FusionReviewForm extends FusionElement
{
    public static function elementParameters(): array
    {
        return [
            'assigned_posts' => [
                'default' => '',
                'heading' => esc_attr_x('Assign Review to a Page', 'admin-text', 'site-reviews'),
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
            'assigned_terms' => static::optionAssignedTerms(esc_attr_x('Assign Review to Categories', 'admin-text', 'site-reviews')),
            'assigned_users' => [
                'default' => '',
                'heading' => esc_attr_x('Assign Review to a User', 'admin-text', 'site-reviews'),
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
            'hide' => [
                'default' => '',
                'heading' => esc_html_x('Hide Fields', 'admin-text', 'site-reviews'),
                'param_name' => 'hide',
                'placeholder_text' => esc_attr_x('Select Fields to Hide', 'admin-text', 'site-reviews'),
                'type' => 'multiple_select',
                'value' => glsr(SiteReviewsFormShortcode::class)->getHideOptions(),
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
            'reviews_id' => [
                'heading' => esc_attr_x('Reviews CSS ID', 'admin-text', 'site-reviews'),
                'description' => esc_attr_x('Enter the CSS ID of a Latest Reviews element where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'param_name' => 'reviews_id',
                'type' => 'textfield',
                'value' => '',
            ],
        ];
    }

    public static function registerElement(): void
    {
        $parameters = static::elementParameters();
        $parameters = glsr()->filterArray('fusion-builder/controls/site_reviews_form', $parameters);
        fusion_builder_map(fusion_builder_frontend_data(static::class, [
            'name' => esc_attr_x('Review Form', 'admin-text', 'site-reviews'),
            'shortcode' => 'site_reviews_form',
            'icon' => 'fusiona-af-rating',
            'params' => $parameters,
        ]));
    }
}
