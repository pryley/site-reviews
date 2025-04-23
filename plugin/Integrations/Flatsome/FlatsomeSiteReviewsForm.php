<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsFormShortcode;

class FlatsomeSiteReviewsForm extends FlatsomeShortcode
{
    public function options(): array
    {
        return [
            'reviews_id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Rating Summary ID', 'admin-text', 'site-reviews'),
                'description' => _x('Enter the Custom ID of a Latest Reviews block where the review should be displayed after submission.', 'admin-text', 'site-reviews'),
                'full_width' => true,
            ],
            'summary_id' => [
                'type' => 'textfield',
                'heading' => esc_html_x('Latest Reviews ID', 'admin-text', 'site-reviews'),
                'description' => _x('Enter the Custom ID of a Rating Summary block where the rating values should be updated after submission.', 'admin-text', 'site-reviews'),
                'full_width' => true,
            ],
            'glsr_group_assignment' => [
                'type' => 'group',
                'heading' => esc_html_x('Review Assignment', 'admin-text', 'site-reviews'),
                'options' => [
                    'assigned_posts' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Assign New Reviews to Pages', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'config' => [
                            'multiple' => true,
                            'placeholder' => esc_html_x('Select a Page...', 'admin-text', 'site-reviews'),
                            'postSelect' => 'assigned_posts_query',
                        ],
                    ],
                    'assigned_terms' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Assign New Reviews to Categories', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'config' => [
                            'multiple' => true,
                            'placeholder' => esc_html_x('Select a Category...', 'admin-text', 'site-reviews'),
                            'termSelect' => [
                                'taxonomies' => glsr()->taxonomy,
                            ],
                        ],
                    ],
                    'assigned_users' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Assign New Reviews to Users', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'config' => [
                            'multiple' => true,
                            'placeholder' => esc_html_x('Select a User...', 'admin-text', 'site-reviews'),
                            'postSelect' => 'assigned_users_query',
                        ],
                    ],
                ],
            ],
            'glsr_group_hide' => [
                'type' => 'group',
                'heading' => esc_html_x('Hide Options', 'admin-text', 'site-reviews'),
                'options' => $this->hideOptions(),
            ],
            'glsr_group_advanced' => [
                'type' => 'group',
                'heading' => esc_html_x('Advanced', 'admin-text', 'site-reviews'),
                'options' => [
                    'id' => [
                        'type' => 'textfield',
                        'heading' => esc_html_x('Custom ID', 'admin-text', 'site-reviews'),
                        'description' => esc_html_x('This should be a unique value.', 'admin-text', 'site-reviews'),
                        'full_width' => true,
                    ],
                    'class' => [
                        'type' => 'textfield',
                        'heading' => esc_html_x('Additional CSS classes', 'admin-text', 'site-reviews'),
                        'description' => esc_html_x('Separate multiple classes with spaces.', 'admin-text', 'site-reviews'),
                        'full_width' => true,
                    ],
                    'visibility' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Visibility', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'options' => [
                            '' => esc_html_x('Visible', 'admin-text', 'site-reviews'),
                            'hidden' => esc_html_x('Hidden', 'admin-text', 'site-reviews'),
                            'hide-for-medium' => esc_html_x('Only for Desktop', 'admin-text', 'site-reviews'),
                            'show-for-small' => esc_html_x('Only for Mobile', 'admin-text', 'site-reviews'),
                            'show-for-medium hide-for-small' => esc_html_x('Only for Tablet', 'admin-text', 'site-reviews'),
                            'show-for-medium' => esc_html_x('Hide for Desktop', 'admin-text', 'site-reviews'),
                            'hide-for-small' => esc_html_x('Hide for Mobile', 'admin-text', 'site-reviews'),
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function shortcodeClass(): string
    {
        return SiteReviewsFormShortcode::class;
    }

    protected function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-form.svg');
    }
}
