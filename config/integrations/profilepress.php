<?php

if (!function_exists('get_editable_roles')) {
    require_once ABSPATH.'wp-admin/includes/user.php';
}

$roles = array_map('translate_user_role', wp_list_pluck(get_editable_roles(), 'name'));
ksort($roles);

return [ // order is intentional
    'settings.integrations.profilepress.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will enable the ProfilePress integration with Site Reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.profilepress.directory_display_empty' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
        ],
        'label' => _x('Display Empty Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display empty ratings on the Member Directory page for profiles with no reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.profilepress.directory_sorting' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
        ],
        'description' => sprintf('<span class="dashicons dashicons-arrow-right"></span> %s<br><span class="dashicons dashicons-arrow-right"></span> %s',
            sprintf('<a href="https://www.xkcd.com/937/" target="_blank">%s</a>', _x('The problem with averaging star ratings', 'admin-text', 'site-reviews')),
            sprintf('<a href="https://fulmicoton.com/posts/bayesian_rating/" target="_blank">%s</a>', _x('Of bayesian average and star ratings', 'admin-text', 'site-reviews'))
        ),
        'label' => _x('Member Sorting', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Average Rating', 'admin-text', 'site-reviews'),
            'bayesian' => _x('Bayesian Ranking', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('This is the method used when sorting members by rating on the Members Directory page.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.profilepress.display_account_tab' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
        ],
        'label' => _x('Display Account Reviews?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display a reviews tab on the Account page.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.profilepress.account_tab_reviews' => [
        'class' => 'large-text',
        'default' => '[site_reviews author="user_id" display="12" pagination="ajax"]',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_account_tab' => ['yes'],
        ],
        'label' => _x('Account Reviews Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews author="user_id" display="12" pagination="ajax"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the Latest Reviews shortcode used on the Account page.', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.profilepress.account_tab_visibility' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_account_tab' => ['yes'],
        ],
        'label' => _x('Account Reviews Visibility', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Everyone', 'admin-text', 'site-reviews'),
            'roles' => _x('Only Specific Roles', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('Choose who can view the reviews tab on their Account page.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.profilepress.account_tab_roles' => [
        'class' => 'regular-grid',
        'default' => ['administrator'],
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_account_tab' => ['yes'],
            'settings.integrations.profilepress.account_tab_visibility' => ['owner_roles', 'roles'],
        ],
        'label' => _x('Account Reviews Roles', 'admin-text', 'site-reviews'),
        'options' => $roles,
        'sanitizer' => 'array-string',
        'tooltip' => _x('Choose which user roles are allowed to view the reviews tab on their Account page.', 'admin-text', 'site-reviews'),
        'type' => 'checkbox',
    ],
    'settings.integrations.profilepress.display_profile_tab' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
        ],
        'label' => _x('Display Profile Reviews?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display a reviews tab on Profile pages.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.profilepress.profile_tab_summary' => [
        'class' => 'large-text',
        'default' => '[site_reviews_summary assigned_users="profile_id" id="pp_summary_id"]',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_profile_tab' => ['yes'],
        ],
        'label' => _x('Profile Summary Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews_summary assigned_users="profile_id" id="pp_summary_id"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the Rating Summary shortcode used on the Profile page.', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.profilepress.profile_tab_reviews' => [
        'class' => 'large-text',
        'default' => '[site_reviews assigned_users="profile_id" hide="assigned_links" id="pp_reviews_id" pagination="loadmore"]',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_profile_tab' => ['yes'],
        ],
        'label' => _x('Profile Reviews Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews assigned_users="profile_id" hide="assigned_links" id="pp_reviews_id" pagination="loadmore"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the Latest Reviews shortcode used on the Profile page.', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.profilepress.profile_tab_form' => [
        'class' => 'large-text',
        'default' => '[site_reviews_form assigned_users="profile_id" hide="name,email,images" reviews_id="pp_reviews_id" summary_id="pp_summary_id"]',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_profile_tab' => ['yes'],
        ],
        'label' => _x('Profile Form Shortcode', 'admin-text', 'site-reviews'),
        'placeholder' => '[site_reviews_form assigned_users="profile_id" hide="name,email,images" reviews_id="pp_reviews_id" summary_id="pp_summary_id"]',
        'sanitizer' => 'text',
        'tooltip' => _x('Enter the Review Form shortcode used on the Profile page.', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
    'settings.integrations.profilepress.profile_tab_visibility' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_profile_tab' => ['yes'],
        ],
        'label' => _x('Profile Reviews Visibility', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Anyone', 'admin-text', 'site-reviews'),
            'guest' => _x('Only Guests', 'admin-text', 'site-reviews'),
            'member' => _x('Only Members', 'admin-text', 'site-reviews'),
            'roles' => _x('Only Specific Roles', 'admin-text', 'site-reviews'),
            'owner' => _x('Only the Profile Owner', 'admin-text', 'site-reviews'),
            'owner_roles' => _x('Only the Profile Owner and Specific Roles', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('Choose who can view the reviews tab on Profile pages.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.profilepress.profile_tab_roles' => [
        'class' => 'regular-grid',
        'default' => ['administrator'],
        'depends_on' => [
            'settings.integrations.profilepress.enabled' => ['yes'],
            'settings.integrations.profilepress.display_profile_tab' => ['yes'],
            'settings.integrations.profilepress.profile_tab_visibility' => ['owner_roles', 'roles'],
        ],
        'label' => _x('Profile Reviews Roles', 'admin-text', 'site-reviews'),
        'options' => $roles,
        'sanitizer' => 'array-string',
        'tooltip' => _x('Choose which user roles are allowed to view the reviews tab on Profile pages.', 'admin-text', 'site-reviews'),
        'type' => 'checkbox',
    ],
];
