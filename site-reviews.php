<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝
 *
 * Plugin Name: Site Reviews
 * Plugin URI:  https://wordpress.org/plugins/site-reviews
 * Description: Receive and display reviews on your website
 * Version:     3.0.4
 * Author:      Paul Ryley
 * Author URI:  https://niftyplugins.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: site-reviews
 * Domain Path: languages
 */

defined( 'WPINC' ) || die;

if( !class_exists( 'GL_Plugin_Check_v3' )) {
	require_once __DIR__.'/activate.php';
}
if( !(new GL_Plugin_Check_v3( __FILE__ ))->canProceed() )return;
require_once __DIR__.'/autoload.php';
require_once __DIR__.'/compatibility.php';
require_once __DIR__.'/deprecated.php';
require_once __DIR__.'/helpers.php';

$app = new GeminiLabs\SiteReviews\Application;
$app->make( 'Provider' )->register( $app );
register_activation_hook( __FILE__, array( $app, 'activate' ));
register_deactivation_hook( __FILE__, array( $app, 'deactivate' ));
register_shutdown_function( array( $app, 'catchFatalError' ));
$app->init();
