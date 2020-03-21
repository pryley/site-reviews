<?php

defined('WPINC') || die;

/*
 * @return void
 * @see https://wordpress.org/plugins/members/
 */
add_action('members_register_caps', function () {
    members_register_cap('delete_others_site-reviews', [
        'label' => _x("Delete Others' Reviews", 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_site-reviews', [
        'label' => _x('Delete Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_private_site-reviews', [
        'label' => _x('Delete Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('delete_published_site-reviews', [
        'label' => _x('Delete Approved Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_others_site-reviews', [
        'label' => _x("Edit Others' Reviews", 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_site-reviews', [
        'label' => _x('Edit Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_private_site-reviews', [
        'label' => _x('Edit Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('edit_published_site-reviews', [
        'label' => _x('Edit Approved Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('publish_site-reviews', [
        'label' => _x('Approve Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('read_private_site-reviews', [
        'label' => _x('Read Private Reviews', 'admin-text', 'site-reviews'),
    ]);
    members_register_cap('create_site-review', [
        'label' => _x('Create Review (inactive)', 'admin-text', 'site-reviews'),
    ]);
});

/*
 * @param \GeminiLabs\SiteReviews\Modules\Html\Builder $instance
 * @return void
 * @see https://www.elegantthemes.com/gallery/divi/
 */
add_action('site-reviews/customize/divi', function ($instance) {
    if ('label' != $instance->tag || 'checkbox' != $instance->args['type']) {
        return;
    }
    $instance->args['text'] = '<i></i>'.$instance->args['text'];
});

/*
 * Clears the WP-Super-Cache plugin cache after a review has been submitted
 * @param \GeminiLabs\SiteReviews\Review $review
 * @param \GeminiLabs\SiteReviews\Commands\CreateReview $request
 * @return void
 * @see https://wordpress.org/plugins/wp-super-cache/
 */
add_action('site-reviews/review/created', function ($review, $request) {
    if (!function_exists('wp_cache_post_change')) {
        return;
    }
    wp_cache_post_change($request->post_id);
    if (empty($review->assigned_to) || $review->assigned_to == $request->post_id) {
        return;
    }
    wp_cache_post_change($review->assigned_to);
}, 10, 2);

/*
 * @param array $scriptHandles
 * @return array
 * @see https://wordpress.org/plugins/speed-booster-pack/
 */
add_filter('sbp_exclude_defer_scripts', function ($scriptHandles) {
    $scriptHandles[] = 'site-reviews/google-recaptcha';
    return array_keys(array_flip($scriptHandles));
});

/*
 * Fix to display all reviews when sorting by rank
 * @param array $query
 * @return array
 * @see https://searchandfilter.com/
 */
add_filter('sf_edit_query_args', function ($query) {
    if (!empty($query['meta_key']) && '_glsr_ranking' == $query['meta_key']) {
        unset($query['meta_key']);
        $query['meta_query'] = [
            'relation' => 'OR',
            ['key' => '_glsr_ranking', 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => '_glsr_ranking', 'compare' => 'EXISTS'],
        ];
    }
    return $query;
}, 20);
