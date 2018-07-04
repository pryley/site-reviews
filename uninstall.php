<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || die;

require_once __DIR__.'/site-reviews.php';
if( !(new GL_Plugin_Check_v3( __FILE__ ))->isValid() )return;

delete_option( GeminiLabs\SiteReviews\Database\OptionManager::databaseKey() );
delete_option( 'widget_'.glsr()->id.'_site-reviews' );
delete_option( 'widget_'.glsr()->id.'_site-reviews-form' );
delete_option( 'widget_'.glsr()->id.'_site-reviews-summary' );
delete_transient( glsr()->id.'_cloudflare_ips' );
delete_transient( glsr()->id.'_remote_post_test' );
wp_cache_delete( glsr()->id );

glsr( 'Modules\Session' )->deleteAllSessions();
