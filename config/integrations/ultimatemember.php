<?php

if (!function_exists('get_editable_roles')) {
    require_once ABSPATH.'wp-admin/includes/user.php';
}

$roles = array_map('translate_user_role', wp_list_pluck(get_editable_roles(), 'name'));
ksort($roles);

return [ // order is intentional
    'settings.integrations.ultimatemember.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will enable the Ultimate Member integration with Site Reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.ultimatemember.display_directory_ratings' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
        ],
        'label' => _x('Display Directory Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the average rating of each person in the Member Directory.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.ultimatemember.display_empty' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_directory_ratings' => ['yes'],
        ],
        'label' => _x('Display Empty Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the rating stars even if the member has no reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.ultimatemember.sorting' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_directory_ratings' => ['yes'],
        ],
        'label' => _x('Member Sorting', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Average Rating', 'admin-text', 'site-reviews'),
            'bayesian' => _x('Bayesian Ranking', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('This is the method used when sorting members by rating on the Members Directory page.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.ultimatemember.display_reviews_tab' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
        ],
        'label' => _x('Display Reviews Tab?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the reviews tab in member profiles.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.ultimatemember.summary' => [
        'class' => 'large-text',
        'default' => '[site_reviews_summary assigned_users="profile_id"]',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_reviews_tab' => ['yes'],
        ],
        'label' => _x('Summary Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews_summary assigned_users="profile_id"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the rating summary shortcode used on the member profile page', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.ultimatemember.reviews' => [
        'class' => 'large-text',
        'default' => '[site_reviews assigned_users="profile_id" hide="assigned_links" pagination="loadmore"]',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_reviews_tab' => ['yes'],
        ],
        'label' => _x('Reviews Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews assigned_users="profile_id" hide="assigned_links" pagination="loadmore" id="user_reviews"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the latest reviews shortcode used on the member profile page', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.ultimatemember.form' => [
        'class' => 'large-text',
        'default' => '[site_reviews_form assigned_users="profile_id" hide="name,email,images" reviews_id="user_reviews"]',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_reviews_tab' => ['yes'],
        ],
        'label' => _x('Form Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews_form assigned_users="profile_id" hide="name,email,images"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the form shortcode used on the member profile page', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.ultimatemember.reviews_tab_visibility' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_reviews_tab' => ['yes'],
        ],
        'label' => _x('Reviews Tab Visibility', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Anyone', 'admin-text', 'site-reviews'),
            'guest' => _x('Only Guests', 'admin-text', 'site-reviews'),
            'member' => _x('Only Members', 'admin-text', 'site-reviews'),
            'roles' => _x('Only Specific Roles', 'admin-text', 'site-reviews'),
            'owner' => _x('Only the Profile Owner', 'admin-text', 'site-reviews'),
            'owner_roles' => _x('Only the Profile Owner and Specific Roles', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('Choose who can view the reviews tab on member profiles.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.ultimatemember.reviews_tab_roles' => [
        'class' => 'regular-grid',
        'default' => ['administrator'],
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
            'settings.integrations.ultimatemember.display_reviews_tab' => ['yes'],
            'settings.integrations.ultimatemember.reviews_tab_visibility' => ['owner_roles', 'roles'],
        ],
        'label' => _x('Reviews Tab Visibility Roles', 'admin-text', 'site-reviews'),
        'options' => $roles,
        'sanitizer' => 'array-string',
        'tooltip' => _x('Choose which user roles are allowed to view the reviews tab on member profiles.', 'admin-text', 'site-reviews'),
        'type' => 'checkbox',
    ],
];
