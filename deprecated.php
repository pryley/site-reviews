<?php

defined('WPINC') || die;

add_action('plugins_loaded', function () {
    if (!glsr()->filterBool('support/deprecated/v4', true)) {
        return;
    }
    // Unprotected review meta has been deprecated
    add_filter('get_post_metadata', function ($data, $postId, $metaKey, $single) {
        $metaKeys = array_keys(glsr('Defaults\CreateReviewDefaults')->defaults());
        if (!in_array($metaKey, $metaKeys) || glsr()->post_type != get_post_type($postId)) {
            return $data;
        }
        $message = sprintf(
            'The "%1$s" meta_key has been deprecated for Reviews. Please use the protected "_%1$s" meta_key instead.',
            $metaKey
        );
        glsr()->append('deprecated', $message);
        return get_post_meta($postId, '_'.$metaKey, $single);
    }, 10, 4);

    // Modules/Html/Template.php
    add_filter('site-reviews/interpolate/reviews', function ($context, $template) {
        $search = '{{ navigation }}';
        if (false !== strpos($template, $search)) {
            $context['navigation'] = $context['pagination'];
            $message = 'The {{ navigation }} template key in "YOUR_THEME/site-reviews/reviews.php" has been deprecated. Please use the {{ pagination }} template key instead.';
            glsr()->append('deprecated', $message);
        }
        return $context;
    }, 10, 2);

    // Modules/Html/Template.php
    add_filter('site-reviews/build/template/reviews', function ($template) {
        if (has_filter('site-reviews/reviews/pagination-wrapper')) {
            $message = 'The "site-reviews/reviews/pagination-wrapper" hook has been removed. Please use the "site-reviews/builder/result" hook instead.';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/reviews/reviews-wrapper')) {
            $message = 'The "site-reviews/reviews/reviews-wrapper" hook has been removed. Please use the "site-reviews/builder/result" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $template;
    });

    // Database/ReviewManager.php
    add_action('site-reviews/review/created', function ($review) {
        if (has_action('site-reviews/local/review/create')) {
            $message = 'The "site-reviews/local/review/create" hook has been deprecated. Please use the "site-reviews/review/created" hook instead.';
            glsr()->append('deprecated', $message);
            glsr()->action('local/review/create', (array) get_post($review->ID), (array) $review, $review->ID);
        }
    }, 9);

    // Commands/CreateReview.php
    add_action('site-reviews/review/submitted', function ($review) {
        if (has_action('site-reviews/local/review/submitted')) {
            $message = 'The "site-reviews/local/review/submitted" hook has been deprecated. Please use the "site-reviews/review/submitted" hook instead.';
            glsr()->append('deprecated', $message);
            glsr()->action('local/review/submitted', null, $review);
        }
        if (has_filter('site-reviews/local/review/submitted/message')) {
            $message = 'The "site-reviews/local/review/submitted/message" hook has been deprecated.';
            glsr()->append('deprecated', $message);
        }
    }, 9);

    // Database/ReviewManager.php
    add_filter('site-reviews/create/review-values', function ($values, $command) {
        if (has_filter('site-reviews/local/review')) {
            $message = 'The "site-reviews/local/review" hook has been deprecated. Please use the "site-reviews/create/review-values" hook instead.';
            glsr()->append('deprecated', $message);
            return glsr()->filterArray('local/review', $values, $command);
        }
        return $values;
    }, 9, 2);

    // Commands/EnqueuePublicAssets.php
    add_filter('site-reviews/enqueue/public/localize', function ($variables) {
        if (has_filter('site-reviews/enqueue/localize')) {
            $message = 'The "site-reviews/enqueue/localize" hook has been deprecated. Please use the "site-reviews/enqueue/public/localize" hook instead.';
            glsr()->append('deprecated', $message);
            return glsr()->filterArray('enqueue/localize', $variables);
        }
        return $variables;
    }, 9);

    // Modules/Rating.php
    add_filter('site-reviews/rating/average', function ($average) {
        if (has_filter('site-reviews/average/rating')) {
            $message = 'The "site-reviews/average/rating" hook has been deprecated. Please use the "site-reviews/rating/average" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $average;
    }, 9);

    // Modules/Rating.php
    add_filter('site-reviews/rating/ranking', function ($ranking) {
        if (has_filter('site-reviews/bayesian/ranking')) {
            $message = 'The "site-reviews/bayesian/ranking" hook has been deprecated. Please use the "site-reviews/rating/ranking" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $ranking;
    }, 9);

    // Modules/Html/Partials/SiteReviews.php
    add_filter('site-reviews/review/build/after', function ($renderedFields) {
        if (has_filter('site-reviews/reviews/review/text')) {
            $message = 'The "site-reviews/reviews/review/text" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/reviews/review/title')) {
            $message = 'The "site-reviews/reviews/review/title" hook has been deprecated. Please use the "site-reviews/review/build/after" hook instead.';
            glsr()->append('deprecated', $message);
        }
        return $renderedFields;
    }, 9);

    // Modules/Html/Partials/SiteReviews.php
    add_filter('site-reviews/review/build/before', function ($review) {
        if (has_filter('site-reviews/rendered/review')) {
            $message = 'The "site-reviews/rendered/review" hook has been deprecated. Please either use a custom "review.php" template (refer to the documentation), or use the "site-reviews/review/build/after" hook instead.';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/rendered/review/meta/order')) {
            $message = 'The "site-reviews/rendered/review/meta/order" hook has been deprecated. Please use a custom "review.php" template instead (refer to the documentation).';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/rendered/review/order')) {
            $message = 'The "site-reviews/rendered/review/order" hook has been deprecated. Please use a custom "review.php" template instead (refer to the documentation).';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/rendered/review-form/login-register')) {
            $message = 'The "site-reviews/rendered/review-form/login-register" hook has been deprecated. Please use a custom "login-register.php" template instead (refer to the documentation).';
            glsr()->append('deprecated', $message);
        }
        if (has_filter('site-reviews/reviews/navigation_links')) {
            $message = 'The "site-reviews/reviews/navigation_links" hook has been deprecated. Please use a custom "pagination.php" template instead (refer to the documentation).';
            glsr()->append('deprecated', $message);
        }
        return $review;
    }, 9);

    add_filter('site-reviews/validate/custom', function ($result, $request) {
        if (has_filter('site-reviews/validate/review/submission')) {
            $message = 'The "site-reviews/validate/review/submission" hook has been deprecated. Please use the "site-reviews/validate/custom" hook instead.';
            glsr()->append('deprecated', $message);
            return glsr()->filterBool('validate/review/submission', $result, $request);
        }
        return $result;
    }, 9, 2);

    add_filter('site-reviews/views/file', function ($file, $view, $data) {
        if (has_filter('site-reviews/addon/views/file')) {
            $message = 'The "site-reviews/addon/views/file" hook has been deprecated. Please use the "site-reviews/views/file" hook instead.';
            glsr()->append('deprecated', $message);
            $file = glsr()->filterString('addon/views/file', $file, $view, $data);
        }
        return $file;
    }, 9, 3);
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
function glsr_calculate_ratings()
{
    glsr_log()->error(sprintf(
        _x('The %s function has been deprecated and removed, please update your code.', 'admin-text', 'site-reviews'), 
        'glsr_calculate_ratings()'
    ));
}

/**
 * @return object
 */
function glsr_get_rating($args = array())
{
    glsr_log()->warning(sprintf(
        _x('The %s function has been deprecated and will be removed in a future version, please use %s instead.', 'admin-text', 'site-reviews'), 
        'glsr_get_rating()',
        'glsr_get_ratings()'
    ));
    return glsr_get_ratings($args);
}
