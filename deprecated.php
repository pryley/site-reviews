<?php

use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;

defined('WPINC') || die;

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v5', true)) {
        return;
    }
    /**
     * Review meta data has been deprecated
     * @since 5.0.0
     */
    add_filter('get_post_metadata', function ($data, $postId, $metaKey) {
        if (glsr()->post_type !== get_post_type($postId)) {
            return $data;
        }
        $metaKey = Str::removePrefix('_', $metaKey);
        $metaKeys = array_keys(glsr(CreateReviewDefaults::class)->defaults());
        if (!in_array($metaKey, $metaKeys)) {
            return $data;
        }
        $message = 'Site Reviews no longer stores review values as meta data. Please use the glsr_get_review() helper function instead.';
        glsr()->append('deprecated', $message);
        return glsr_get_review($postId)->{$metaKey};
    }, 10, 3);
});

add_action('admin_footer', 'glsr_log_deprecated_notices');
add_action('wp_footer', 'glsr_log_deprecated_notices');

function glsr_log_deprecated_notices() {
    $notices = (array) glsr()->retrieve('deprecated', []);
    $notices = array_keys(array_flip(array_filter($notices)));
    natsort($notices);
    foreach ($notices as $notice) {
        glsr_log()->warning($notice);
    }
}

/**
 * @return void
 * @since 5.0.0
 */
function glsr_calculate_ratings() {
    glsr_log()->error(sprintf(
        _x('The %s function has been deprecated and removed, please update your code.', 'admin-text', 'site-reviews'), 
        'glsr_calculate_ratings()'
    ));
}

/**
 * @return object
 * @since 5.0.0
 */
function glsr_get_rating($args = array()) {
    glsr_log()->warning(sprintf(
        _x('The %s function has been deprecated and will be removed in a future version, please use %s instead.', 'admin-text', 'site-reviews'), 
        'glsr_get_rating()',
        'glsr_get_ratings()'
    ));
    return glsr_get_ratings($args);
}
