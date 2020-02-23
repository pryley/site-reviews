<?php

defined('WP_UNINSTALL_PLUGIN') || die;

$file = __DIR__.'/site-reviews.php';
require_once $file;

if (!(new GL_Plugin_Check_v4($file))->isValid()) {
    return;
}
delete_option(GeminiLabs\SiteReviews\Database\OptionManager::databaseKey(3));
delete_option(GeminiLabs\SiteReviews\Database\OptionManager::databaseKey(4));
delete_option('_glsr_trustalyze');
delete_option('widget_'.glsr()->id.'_site-reviews');
delete_option('widget_'.glsr()->id.'_site-reviews-form');
delete_option('widget_'.glsr()->id.'_site-reviews-summary');
delete_transient(glsr()->id.'_cloudflare_ips');
delete_transient(glsr()->id.'_remote_post_test');
wp_cache_delete(glsr()->id);

global $wpdb;

$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key = '_glsr_notices'");
