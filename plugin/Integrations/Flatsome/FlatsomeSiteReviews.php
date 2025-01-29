<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class FlatsomeSiteReviews extends FlatsomeShortcode
{
    public function options(): array
    {
        $options = [
            'glsr_group_limit' => [
                'type' => 'group',
                'heading' => esc_html_x('Limit Reviews By', 'admin-text', 'site-reviews'),
                'options' => [
                    'assigned_posts' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Limit Reviews by Assigned Pages', 'admin-text', 'site-reviews'),
                        'full_width' => true,
                        'config' => [
                            'multiple' => true,
                            'placeholder' => esc_html_x('Select a Page...', 'admin-text', 'site-reviews'),
                            'postSelect' => 'assigned_posts_query',
                        ],
                    ],
                    'assigned_terms' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Limit Reviews by Categories', 'admin-text', 'site-reviews'),
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
                        'heading' => esc_html_x('Limit Reviews by Assigned Users', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'config' => [
                            'multiple' => true,
                            'placeholder' => esc_html_x('Select a User...', 'admin-text', 'site-reviews'),
                            'postSelect' => 'assigned_users_query',
                        ],
                    ],
                    'terms' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Limit Reviews by Accepted Terms', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'options' => [
                            '' => esc_html_x('Select Terms...', 'admin-text', 'site-reviews'),
                            'true' => esc_html_x('Terms were accepted', 'admin-text', 'site-reviews'),
                            'false' => esc_html_x('Terms were not accepted', 'admin-text', 'site-reviews'),
                        ],
                    ],
                ],
            ],
            'glsr_group_display' => [
                'type' => 'group',
                'heading' => esc_html_x('Display Options', 'admin-text', 'site-reviews'),
                'options' => [
                    'pagination' => [
                        'type' => 'select',
                        'heading' => esc_html_x('Pagination Type', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'options' => [
                            '' => esc_attr_x('No Pagination', 'admin-text', 'site-reviews'),
                            'loadmore' => esc_attr_x('Load More Button', 'admin-text', 'site-reviews'),
                            'ajax' => esc_attr_x('Pagination (AJAX)', 'admin-text', 'site-reviews'),
                            'true' => esc_attr_x('Pagination (with page reload)', 'admin-text', 'site-reviews'),
                        ],
                    ],
                    'display' => [
                        'type' => 'slider',
                        'heading' => esc_html_x('Reviews Per Page', 'admin-text', 'site-reviews'),
                        'default' => 10,
                        'max' => 50,
                        'min' => 1,
                        'full_width' => true,
                    ],
                    'rating' => [
                        'type' => 'slider',
                        'heading' => esc_html_x('Minimum Rating', 'admin-text', 'site-reviews'),
                        'default' => Rating::min(),
                        'max' => Rating::max(),
                        'min' => Rating::min(),
                        'full_width' => true,
                    ],
                    'schema' => [
                        'type' => 'radio-buttons',
                        'heading' => esc_html_x('Enable the schema?', 'admin-text', 'site-reviews'),
                        'description' => esc_html_x('The schema should only be enabled once on your page.', 'admin-text', 'site-reviews'),
                        'default' => '',
                        'full_width' => true,
                        'options' => [
                            '' => ['title' => _x('No', 'admin-text', 'site-reviews')],
                            'true' => ['title' => _x('Yes', 'admin-text', 'site-reviews')],
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
        if ($types = $this->typeOptions()) {
            $options['glsr_group_limit']['options']['type'] = $types;
        }
        return $options;
    }

    protected function icon(): string
    {
        return glsr()->url('assets/images/icons/flatsome/icon-reviews.svg');
    }

    protected function shortcode(): ShortcodeContract
    {
        return glsr(SiteReviewsShortcode::class);
    }
}
