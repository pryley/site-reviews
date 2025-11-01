<?php

return [ // order is intentional
    'settings.integrations.woocommerce.enabled' => [
        'default' => 'no',
        'label' => _x('Enable Integration?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        /* translators: %s is replaced with a link to the Import Reviews tool in Site Reviews */
        'tooltip' => sprintf(_x('This will completely replace the default WooCommerce review system with Site Reviews. If you have existing WooCommerce comment reviews, you may need to first export them to a CSV file, and then import them using the %s tool.', 'admin-text', 'site-reviews'),
            glsr_admin_link('tools.general', _x('Import Reviews', 'admin-text', 'site-reviews'), '#tools-import-reviews')
        ),
        'type' => 'yes_no',
    ],
    'settings.integrations.woocommerce.style' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'label' => _x('Rating Style', 'admin-text', 'site-reviews'),
        'options' => [
            '' => _x('Site Reviews (default)', 'admin-text', 'site-reviews'),
            'black' => _x('WooCommerce Black', 'admin-text', 'site-reviews'),
            'woocommerce' => _x('WooCommerce Purple', 'admin-text', 'site-reviews'),
        ],
        'sanitizer' => 'text',
        'tooltip' => _x('This changes the color of the stars and the summary bars', 'admin-text', 'site-reviews'),
        'type' => 'select',
    ],
    'settings.integrations.woocommerce.summary' => [
        'class' => 'large-text',
        'default' => '[site_reviews_summary assigned_posts="post_id" hide="rating" id="prod_rating_summary"]',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'label' => _x('Summary Shortcode', 'admin-text', 'site-reviews'),
        'rows' => 1,
        'sanitizer' => 'text-html',
        'tooltip' => _x('Enter the summary shortcode used on the product page (the schema option is unnecessary)', 'admin-text', 'site-reviews'),
        'type' => 'textarea',
    ],
    'settings.integrations.woocommerce.reviews' => [
        'class' => 'large-text',
        'default' => '[site_reviews assigned_posts="post_id" hide="assigned_links,title" pagination="ajax" schema="true" id="prod_latest_reviews"]',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'label' => _x('Reviews Shortcode', 'admin-text', 'site-reviews'),
        'rows' => 1,
        'sanitizer' => 'text-html',
        'tooltip' => _x('Enter the reviews shortcode used on the product page (the schema option is unnecessary)', 'admin-text', 'site-reviews'),
        'type' => 'textarea',
    ],
    'settings.integrations.woocommerce.form' => [
        'class' => 'large-text',
        'default' => '[site_reviews_form assigned_posts="post_id" hide="title" reviews_id="prod_latest_reviews" summary_id="prod_rating_summary"]',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'label' => _x('Form Shortcode', 'admin-text', 'site-reviews'),
        'rows' => 1,
        'sanitizer' => 'text-html',
        'tooltip' => _x('Enter the form shortcode used on the product page', 'admin-text', 'site-reviews'),
        'type' => 'textarea',
    ],
    'settings.integrations.woocommerce.sorting' => [
        'class' => 'regular-text',
        'default' => '',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
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
    'settings.integrations.woocommerce.display_empty' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'label' => _x('Display Empty Ratings?', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'tooltip' => _x('This will display the rating stars even if the product has no reviews.', 'admin-text', 'site-reviews'),
        'type' => 'yes_no',
    ],
    'settings.integrations.woocommerce.wp_comments' => [
        'default' => 'no',
        'depends_on' => [
            'settings.integrations.woocommerce.enabled' => ['yes'],
        ],
        'description' => _x('This may fix issues with other plugins which query WooCommerce product reviews. Keep in mind that enabling this option may also cause conflicts with incompatible plugins.', 'admin-text', 'site-reviews'),
        'label' => _x('Filter Comment Queries', 'admin-text', 'site-reviews'),
        'tooltip' => _x('This will filter the output of the wp_comments() function when used to query WooCommerce product reviews.', 'admin-text', 'site-reviews'),
        'sanitizer' => 'text',
        'type' => 'yes_no',
    ],
];
