<?php

defined('ABSPATH') || exit;

/**
 * Provide support for the deprecated {{ assigned_to }} tag
 * @param string $template
 * @return string
 * @since 5.0
 */
add_filter('site-reviews/build/template/review', function ($template) {
    return str_replace('{{ assigned_to }}', '{{ assigned_links }}', $template);
});

/**
 * Fix the {{ review_id }} tag in the review template which now only returns the ID
 * @param string $template
 * @return string
 * @since 5.3
 */
add_filter('site-reviews/build/template/review', function ($template) {
    return str_replace('id="{{ review_id }}"', 'id="review-{{ review_id }}"', $template);
});

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v7', true)) {
        return;
    }

    /**
     * @since 7.0.0
     */
    add_filter('site-reviews/review-form/fields/all', function ($fields, $args) {
        if (has_filter('site-reviews/review-form/fields/normalized')) {
            $message = 'The "site-reviews/review-form/fields/normalized" hook has been deprecated. Please use the "site-reviews/review-form/fields/all" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/review-form/fields/normalized', $fields, $args);
        }
        return $fields;
    }, 10, 2);
});

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v6', true)) {
        return;
    }

    /**
     * @since 6.5.0
     */
    add_filter('site-reviews/rest-api/reviews/schema/properties', function ($properties) {
        if (has_filter('site-reviews/rest-api/reviews/properties')) {
            $message = 'The "site-reviews/rest-api/reviews/properties" hook has been deprecated. Please use the "site-reviews/rest-api/reviews/schema/properties" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/rest-api/reviews/properties', $properties);
        }
        return $properties;
    }, 9);

    /**
     * @since 6.5.0
     */
    add_filter('site-reviews/rest-api/summary/schema/properties', function ($properties) {
        if (has_filter('site-reviews/rest-api/summary/properties')) {
            $message = 'The "site-reviews/rest-api/summary/properties" hook has been deprecated. Please use the "site-reviews/rest-api/summary/schema/properties" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/rest-api/summary/properties', $properties);
        }
        return $properties;
    }, 9);

    /**
     * @since 6.5.0
     */
    add_filter('site-reviews/reviews/html/style', function ($value, $html) {
        return glsr_get($html->reviews->attributes(), 'class'); // @todo show a deprecation notice?
    }, 10, 2);

    /**
     * @since 6.7.0
     */
    add_action('site-reviews/review/updated', function ($review, $data) {
        if (has_action('site-reviews/review/saved')) {
            $message = 'The "site-reviews/review/saved" hook has been deprecated. Please use the "site-reviews/review/updated" hook instead.';
            glsr()->append('deprecated', $message);
            do_action('site-reviews/review/saved', $review, $data);
        }
    }, 10, 2);

    /**
     * @since 6.9.0
     */
    add_filter('site-reviews/slack/notification', function ($notification) {
        if (has_filter('site-reviews/slack/compose')) {
            $message = 'The "site-reviews/slack/compose" hook has been deprecated. Please use the "site-reviews/slack/notification" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $notification;
    });
});

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v5', true)) {
        return;
    }

    /*
     * Application
     * @since 5.0.0
     */
    add_filter('site-reviews/config/forms/review-form', function ($config) {
        if (has_filter('site-reviews/config/forms/submission-form')) {
            $message = 'The "site-reviews/config/forms/submission-form" hook has been deprecated. Please use the "site-reviews/config/forms/review-form" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/config/forms/submission-form', $config);
        }
        return $config;
    }, 9);

    /*
     * Modules\Html\ReviewsHtml
     * @since 5.0.0
     */
    add_filter('site-reviews/rendered/template/reviews', function ($html) {
        if (has_filter('site-reviews/reviews/reviews-wrapper')) {
            $message = 'The "site-reviews/reviews/reviews-wrapper" hook has been removed. Please use a custom "reviews.php" template instead.';
            glsr()->append('deprecated', $message);
        }
        return $html;
    });

    /**
     * Controllers\PublicController
     * @since 5.0.0
     */
    add_filter('site-reviews/review-form/order', function ($order) {
        if (has_filter('site-reviews/submission-form/order')) {
            $message = 'The "site-reviews/submission-form/order" hook has been deprecated. Please use the "site-reviews/review-form/order" hook instead.';
            glsr()->append('deprecated', $message);
            return apply_filters('site-reviews/submission-form/order', $order);
        }
        return $order;
    }, 9);

    /*
     * Controllers\ListTableController
     * @since 5.11.0
     */
    add_filter('site-reviews/defaults/review-table-filters', function ($defaults) {
        if (has_filter('site-reviews/review-table/filter')) {
            $message = 'The "site-reviews/review-table/filter" hook has been deprecated. Please use the "site-reviews/defaults/review-table-filters" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $defaults;
    });

    /*
     * Database\ReviewManager
     * @since 5.11.0
     */
    add_action('site-reviews/review/responded', function ($review, $response) {
        if (has_action('site-reviews/review/response')) {
            $message = 'The "site-reviews/review/response" hook has been deprecated. Please use the "site-reviews/review/responded" hook instead which is documented on the FAQ page.';
            glsr()->append('deprecated', $message);
            do_action('site-reviews/review/response', $response, $review);
        }
    }, 9, 2);
});

/**
 * @return void
 *
 * @since 5.0.0
 */
function glsr_calculate_ratings()
{
    _deprecated_function('glsr_calculate_ratings', '5.0 (of Site Reviews)');
    glsr_log()->error(sprintf(
        __('%s is <strong>deprecated</strong> since version %s with no alternative available.', 'site-reviews'),
        'glsr_calculate_ratings',
        '5.0'
    ));
}

/**
 * @return object
 *
 * @since 5.0.0
 */
function glsr_get_rating($args = [])
{
    _deprecated_function('glsr_get_rating', '5.0 (of Site Reviews)', 'glsr_get_ratings');
    glsr_log()->notice(sprintf(
        __('%s is <strong>deprecated</strong> since version %s! Use %s instead.', 'site-reviews'),
        'glsr_get_rating',
        '5.0',
        'glsr_get_ratings'
    ));
    return glsr_get_ratings($args);
}

function glsr_log_deprecated_notices()
{
    $notices = glsr()->retrieveAs('array', 'deprecated', []);
    $notices = array_keys(array_flip(array_filter($notices)));
    natsort($notices);
    foreach ($notices as $notice) {
        trigger_error($notice, E_USER_DEPRECATED);
        glsr_log()->notice($notice);
    }
}
add_action('admin_footer', 'glsr_log_deprecated_notices');
add_action('wp_footer', 'glsr_log_deprecated_notices');
