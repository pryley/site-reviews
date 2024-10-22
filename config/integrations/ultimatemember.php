<?php

return [ // order is intentional
    'settings.integrations.ultimatemember.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => sprintf(_x('This will enable the Ultimate Member integration with Site Reviews.', 'admin-text', 'site-reviews'),
            sprintf('<a data-expand="#tools-import-reviews" href="%s">%s</a>', glsr_admin_url('tools', 'general'), _x('Import Reviews', 'admin-text', 'site-reviews'))
        ),
        'type' => 'yes_no',
    ],
    'settings.integrations.ultimatemember.display_directory_ratings' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.ultimatemember.enabled' => ['yes'],
        ],
        'label' => _x('Display Directory Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the rating of each person in the Member Directory.', 'admin-text', 'site-reviews'),
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
        'tooltip' => _x('Enter the rating summary shortcode used on the profile page', 'admin-text', 'site-reviews'),
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
        'tooltip' => _x('Enter the latest reviews shortcode used on the profile page', 'admin-text', 'site-reviews'),
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
        'tooltip' => _x('Enter the form shortcode used on the profile page', 'admin-text', 'site-reviews'),
        'type' => 'text',
    ],
];
