<?php

defined('WP_UNINSTALL_PLUGIN') || die;

global $wpdb;

$app = array(
    'id' => 'site-reviews',
    'prefix' => 'glsr_',
    'post_type' => 'site-review',
    'taxonomy' => 'site-review-category',
);
$settings = get_option($app['id'].'-v5');
$uninstall = isset($settings['general']['delete_data_on_uninstall'])
    ? $settings['general']['delete_data_on_uninstall']
    : '';
$versions = array(
    1 => 'geminilabs_site_reviews_settings',
    2 => 'geminilabs_site_reviews-v2',
    3 => $app['id'].'-v3',
    4 => $app['id'].'-v4',
    5 => $app['id'].'-v5',
);

wp_clear_scheduled_hook('site-reviews/schedule/session/purge'); // @removed in v5.2

if (in_array($uninstall, ['all', 'minimal'])) {
    foreach ($versions as $version) {
        delete_option($version);
    }
    delete_option('theme_mods_'.$app['id']);
    delete_option('widget_'.$app['prefix'].'site-reviews');
    delete_option('widget_'.$app['prefix'].'site-reviews-form');
    delete_option('widget_'.$app['prefix'].'site-reviews-summary');
    delete_option($app['prefix'].'activated');
    delete_transient($app['prefix'].'cloudflare_ips');
    delete_transient($app['prefix'].'migrations');
    delete_transient($app['prefix'].'remote_post_test');
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key = '_glsr_notices'");
}
if ('all' === $uninstall) {
    $likePrefix = '%'.$wpdb->esc_like($app['prefix']).'%';
    $likeTaxonomy = '%'.$wpdb->esc_like($app['taxonomy']).'%';
    // delete all reviews and revisions
    $wpdb->query($wpdb->prepare("
        DELETE p, pr, tr, pm
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->posts} pr ON (p.ID = pr.post_parent AND pr.post_type = 'revision')
        LEFT JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
        LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
        WHERE p.post_type = %s", $app['post_type']
    ));
    // delete all review categories
    $wpdb->query($wpdb->prepare("
        DELETE tt, t, tm
        FROM {$wpdb->term_taxonomy} tt
        LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
        LEFT JOIN {$wpdb->termmeta} tm ON (tt.term_id = tm.term_id)
        WHERE tt.taxonomy = %s", $app['taxonomy']
    ));
    // delete all assigned_posts meta
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $likePrefix));
    // delete all assigned_users meta
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", $likePrefix));
    // delete any remaining options
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $likePrefix));
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $likeTaxonomy));
    // drop all custom tables
    $prefix = $wpdb->prefix.$prefix;
    $wpdb->query("DROP TABLE {$prefix}assigned_posts");
    $wpdb->query("DROP TABLE {$prefix}assigned_terms");
    $wpdb->query("DROP TABLE {$prefix}assigned_users");
    $wpdb->query("DROP TABLE {$prefix}ratings");
    // optimise affected database tables
    $wpdb->query("OPTIMIZE TABLE {$wpdb->options}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->postmeta}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->posts}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->termmeta}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->terms}");
    $wpdb->query("OPTIMIZE TABLE {$wpdb->usermeta}");
    // delete the saved database version
    delete_option($app['prefix'].'db_version');
    // finally, flush the entire cache
    wp_cache_flush();
}
