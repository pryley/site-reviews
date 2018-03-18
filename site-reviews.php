<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Site Reviews
 * Plugin URI:  https://wordpress.org/plugins/site-reviews
 * Description: Receive and display reviews on your website
 * Version:     3.0.0
 * Author:      Paul Ryley
 * Author URI:  https://niftyplugins.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-reviews
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

if( !class_exists( 'GL_Plugin_Check_v1' )) {
	require_once __DIR__.'/activate.php';
}
if( GL_Plugin_Check_v1::shouldDeactivate( __FILE__, array( 'wordpress' => '4.7.0' )))return;

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/helpers.php';

$app = new GeminiLabs\SiteReviews\Application;
$app->make( 'Provider' )->register( $app );
register_activation_hook( __FILE__, array( $app, 'activate' ));
register_deactivation_hook( __FILE__, array( $app, 'deactivate' ));
$app->init();
