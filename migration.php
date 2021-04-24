<?php

use GeminiLabs\SiteReviews\Database;

defined('ABSPATH') || die;

/**
 * @return array
 * @since 5.9
 */
add_filter('site-reviews/config/forms/metabox-fields', function (array $config) {
    if (!glsr(Database::class)->version('1.1')) {
        unset($config['terms']);
    }
    return $config;
});

/**
 * @return array
 * @since 5.9
 */
add_filter('site-reviews/defaults/rating', function (array $defaults) {
    if (!glsr(Database::class)->version('1.1')) {
        unset($defaults['terms']);
    }
    return $defaults;
});
