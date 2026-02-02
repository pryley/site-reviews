<?php

return [ // order is intentional
    'assigned_terms' => [
        'label' => esc_html_x('Assigned Categories', 'admin-text', 'site-reviews'),
        'options' => [
            'id' => esc_html_x('Export as Term IDs', 'admin-text', 'site-reviews'),
            'slug' => esc_html_x('Export as Term Slugs', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'assigned_posts' => [
        'label' => esc_html_x('Assigned Posts', 'admin-text', 'site-reviews'),
        'options' => [
            'id' => esc_html_x('Export as Post IDs', 'admin-text', 'site-reviews'),
            'slug' => sprintf(esc_html_x('Export as %s', 'post_type:slug (admin-text)', 'site-reviews'), 'post_type:slug'),
        ],
        'type' => 'select',
    ],
    'assigned_users' => [
        'label' => esc_html_x('Assigned Users', 'admin-text', 'site-reviews'),
        'options' => [
            'id' => esc_html_x('Export as User IDs', 'admin-text', 'site-reviews'),
            'slug' => esc_html_x('Export as Usernames', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
    'date' => [
        'label' => esc_html_x('Export Reviews After', 'admin-text', 'site-reviews'),
        'type' => 'date',
    ],
    'post_status' => [
        'type' => 'select',
        'label' => esc_html_x('Export Reviews With Status', 'admin-text', 'site-reviews'),
        'options' => [
            '' => esc_html_x('Approved and Unapproved reviews', 'admin-text', 'site-reviews'),
            'publish' => esc_html_x('Approved reviews only', 'admin-text', 'site-reviews'),
            'pending' => esc_html_x('Unapproved reviews only', 'admin-text', 'site-reviews'),
        ],
    ],
    'author_id' => [
        'label' => esc_html_x('Review Author', 'admin-text', 'site-reviews'),
        'options' => [
            'id' => esc_html_x('Export as User ID', 'admin-text', 'site-reviews'),
            'slug' => esc_html_x('Export as Username', 'admin-text', 'site-reviews'),
        ],
        'type' => 'select',
    ],
];
