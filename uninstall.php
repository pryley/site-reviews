<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || die;

require_once __DIR__.'/site-reviews.php';
if( !(new GL_Plugin_Check_v3( __FILE__,
	array( 'php' => '5.4.0', 'wordpress' => '4.0' )
))->isValid() )return;

$majorVersion = explode( '.', glsr_app()->version );
$majorVersion = array_shift( $majorVersion );

delete_option( glsr_app()->prefix.'-v'.$majorVersion );
delete_option( 'widget_'.glsr_app()->id.'_site-reviews' );
delete_option( 'widget_'.glsr_app()->id.'_site-reviews-form' );

glsr_resolve( 'Session' )->deleteAllSessions();
