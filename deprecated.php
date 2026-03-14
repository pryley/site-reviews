<?php

defined('ABSPATH') || exit;

/*
 * Deprecated since v8.0
 */
add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v8', true)) {
        return;
    }
    add_filter('site-reviews/review-form/fields/order', function ($order) {
        return apply_filters_deprecated('site-reviews/review-form/order',
            [$order],
            '8.0',
            'site-reviews/review-form/fields/order'
        );
    });
});

/*
 * Deprecated since v7.0
 */
add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v7', true)) {
        return;
    }
    add_filter('site-reviews/review-form/fields/all', function ($fields, $args) {
        return apply_filters_deprecated('site-reviews/review-form/fields/normalized',
            [$fields, $args],
            '7.0',
            'site-reviews/review-form/fields/all'
        );
    }, 10, 2);
});

/*
 * Deprecated since v6.0
 */
add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v6', true)) {
        return;
    }
    add_filter('site-reviews/rest-api/reviews/schema/properties', function ($properties) {
        return apply_filters_deprecated('site-reviews/rest-api/reviews/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/reviews/schema/properties'
        );
    }, 9);
    add_filter('site-reviews/rest-api/summary/schema/properties', function ($properties) {
        return apply_filters_deprecated('site-reviews/rest-api/summary/properties',
            [$properties],
            '6.5.0',
            'site-reviews/rest-api/summary/schema/properties'
        );
    }, 9);
    add_action('site-reviews/review/updated', function ($review, $data) {
        do_action_deprecated('site-reviews/review/saved',
            [$review, $data],
            '6.7.0',
            'site-reviews/review/updated'
        );
    }, 10, 2);
    add_filter('site-reviews/slack/notification', function ($notification) {
        return apply_filters_deprecated('site-reviews/slack/compose',
            [$notification],
            '6.9.0',
            'site-reviews/slack/notification'
        );
    });
});

/*
 * Deprecated since v5.0
 */
add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v5', true)) {
        return;
    }
    /*
     * Provide support for the deprecated {{ assigned_to }} tag
     * @param string $template
     * @return string
     * @since 5.0
     */
    add_filter('site-reviews/build/template/review', function ($template) {
        return str_replace('{{ assigned_to }}', '{{ assigned_links }}', $template);
    });
    /*
     * Fix the {{ review_id }} tag in the review template which now only returns the ID
     * @param string $template
     * @return string
     * @since 5.3
     */
    add_filter('site-reviews/build/template/review', function ($template) {
        return str_replace('id="{{ review_id }}"', 'id="review-{{ review_id }}"', $template);
    });
    add_filter('site-reviews/config/forms/review-form', function ($config) {
        return apply_filters_deprecated('site-reviews/config/forms/submission-form',
            [$config],
            '5.0',
            'site-reviews/config/forms/review-form'
        );
    }, 9);
    add_filter('site-reviews/rendered/template/reviews', function ($html) {
        return apply_filters_deprecated('site-reviews/reviews/reviews-wrapper',
            [$html],
            '5.0',
            '',
            'Please use a custom "reviews.php" template instead.'
        );
    });
    add_filter('site-reviews/review-form/fields/order', function ($order) {
        return apply_filters_deprecated('site-reviews/submission-form/order',
            [$order],
            '5.0',
            'site-reviews/review-form/fields/order'
        );
    }, 9);
    add_filter('site-reviews/defaults/review-table-filters', function ($defaults) {
        return apply_filters_deprecated('site-reviews/review-table/filter',
            [$defaults],
            '5.11.0',
            'site-reviews/defaults/review-table-filters'
        );
    });
    add_action('site-reviews/review/responded', function ($review, $response) {
        do_action_deprecated('site-reviews/review/response',
            [$review, $response],
            '5.11.0',
            'site-reviews/review/responded',
            'This hook is documented on the FAQ page.'
        );
    }, 9, 2);
});

function glsr_calculate_ratings()
{
    _deprecated_function('glsr_calculate_ratings', '5.0');
}

function glsr_get_rating($args = [])
{
    _deprecated_function('glsr_get_rating', '5.0', 'glsr_get_ratings');
    return new GeminiLabs\SiteReviews\Arguments($args);
}

function glsr_store_deprecated_hook($hook, $replacement, $version, $message)
{
    if (!str_starts_with($hook, glsr()->id)) {
        return;
    }
    if (empty($replacement)) {
        $notice = sprintf(
            'Hook %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
            $hook,
            $version
        );
    } else {
        $notice = sprintf(
            'Hook %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
            $hook,
            $version,
            $replacement
        );
    }
    glsr()->append('deprecated', trim($notice.' '.$message));
}
add_action('deprecated_hook_run', 'glsr_store_deprecated_hook', 10, 4);

function glsr_store_deprecated_function($fn, $replacement, $version)
{
    if (!str_starts_with($fn, glsr()->prefix)) {
        return;
    }
    if (empty($replacement)) {
        $notice = sprintf(
            'Function %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
            $fn,
            $version
        );
    } else {
        $notice = sprintf(
            'Function %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
            $fn,
            $version,
            $replacement
        );
    }
    glsr()->append('deprecated', $notice);
}
add_action('deprecated_function_run', 'glsr_store_deprecated_function', 10, 3);

function glsr_log_deprecated_notices()
{
    $notices = glsr()->retrieveAs('array', 'deprecated', []);
    $notices = array_keys(array_flip(array_filter($notices)));
    natsort($notices);
    array_walk($notices, fn ($notice) => glsr_log()->notice($notice));
}
add_action('admin_footer', 'glsr_log_deprecated_notices');
add_action('wp_footer', 'glsr_log_deprecated_notices');
