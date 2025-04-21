<?php

return [ // order is intentional
    'settings.integrations.surecart.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will integrate Site Reviews with SureCart.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.surecart.style' => [
        'class' => 'regular-text',
        'default' => 'text',
        'depends_on' => [
            'settings.integrations.surecart.enabled' => ['yes'],
        ],
        'label' => _x('Rating Style', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Site Reviews', 'admin-text', 'site-reviews'),
            'text' => _x('Text Color (currentColor)', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('This changes the color/style of the product rating stars', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.surecart.sorting' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.surecart.enabled' => ['yes'],
        ],
        'description' => sprintf('<span class="dashicons dashicons-arrow-right"></span> %s<br><span class="dashicons dashicons-arrow-right"></span> %s',
            sprintf('<a href="https://www.xkcd.com/937/" target="_blank">%s</a>', _x('The problem with averaging star ratings', 'admin-text', 'site-reviews')),
            sprintf('<a href="https://fulmicoton.com/posts/bayesian_rating/" target="_blank">%s</a>', _x('Of bayesian average and star ratings', 'admin-text', 'site-reviews'))
        ),
        'label' => _x('Product Sorting', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Average Rating', 'admin-text', 'site-reviews'),
            'bayesian' => _x('Bayesian Ranking', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('This is the method used to sort products by rating on the shop page.', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.surecart.display_empty' => [
        'default' => 'yes',
        'depends_on' => [
            'settings.integrations.surecart.enabled' => ['yes'],
        ],
        'label' => _x('Display Empty Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the rating stars even if the product has no reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.surecart.ownership' => [
        'default' => ['labeled'],
        'depends_on' => [
            'settings.integrations.surecart.enabled' => ['yes'],
        ],
        'label' => _x('Verified Ownership', 'admin-text', 'site-reviews'),
        'options' => [
            'labeled' => _x('Display a "verified owner" label on customer reviews', 'admin-text', 'site-reviews'),
            'restricted' => _x('Reviews can only be left by customers who purchased the product', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'array-string',
        'tooltip' => _x('Select the Verified Ownership options.', 'admin-text', 'site-reviews'),
        'type' => 'checkbox',
    ],
];
