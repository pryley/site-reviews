<?php

defined('WP_UNINSTALL_PLUGIN') || die;

$file = __DIR__.'/site-reviews.php';
require_once $file;

if (!(new GL_Plugin_Check_v5($file))->isValid()) {
    return;
}

global $wpdb;

$uninstallOption = glsr_get_option('general.delete_data_on_uninstall');

if (in_array($uninstallOption, ['all', 'minimal'])) {
    foreach (range(1, glsr()->version('major')) as $version) {
        delete_option(GeminiLabs\SiteReviews\Database\OptionManager::databaseKey($version));
    }
    delete_option('theme_mods_'.glsr()->id);
    delete_option('widget_'.glsr()->prefix.'site-reviews');
    delete_option('widget_'.glsr()->prefix.'site-reviews-form');
    delete_option('widget_'.glsr()->prefix.'site-reviews-summary');
    delete_option(glsr()->prefix.'activated');
    delete_transient(glsr()->prefix.'cloudflare_ips');
    delete_transient(glsr()->prefix.'migrations');
    delete_transient(glsr()->prefix.'remote_post_test');
    $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key = '_glsr_notices'");
}
if ('all' === $uninstallOption) {
    $likePrefix = '%'.$wpdb->esc_like(glsr()->prefix).'%';
    $likeTaxonomy = '%'.$wpdb->esc_like(glsr()->taxonomy).'%';
    // delete all reviews and revisions
    $wpdb->query($wpdb->prepare("
        DELETE p, pr, tr, pm
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->posts} pr ON (p.ID = pr.post_parent AND pr.post_type = 'revision')
        LEFT JOIN {$wpdb->term_relationships} tr ON (p.ID = tr.object_id)
        LEFT JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
        WHERE p.post_type = %s", glsr()->post_type
    ));
    // delete all review categories
    $wpdb->query($wpdb->prepare("
        DELETE tt, t, tm
        FROM {$wpdb->term_taxonomy} tt
        LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
        LEFT JOIN {$wpdb->termmeta} tm ON (tt.term_id = tm.term_id)
        WHERE tt.taxonomy = %s", glsr()->taxonomy
    ));
    // delete all assigned_posts meta
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $likePrefix));
    // delete all assigned_users meta
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", $likePrefix));
    // delete any remaining options
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $likePrefix));
    $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", $likeTaxonomy));
    // drop all custom tables
    $prefix = $wpdb->prefix.glsr()->prefix;
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
    delete_option(glsr()->prefix.'db_version');
    // finally, flush the entire cache
    wp_cache_flush();
}
