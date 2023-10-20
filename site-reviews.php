<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:          Site Reviews
 * Plugin URI:           https://wordpress.org/plugins/site-reviews
 * Description:          Receive and display reviews on your website
 * Version:              6.11.3
 * Author:               Paul Ryley
 * Author URI:           https://geminilabs.io
 * License:              GPL3
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least:    5.8
 * Requires PHP:         7.2
 * Text Domain:          site-reviews
 * Domain Path:          languages
 * WC requires at least: 6.4
 * WC tested up to:      8.0
 */
defined('ABSPATH') || exit;

if (!class_exists('GL_Plugin_Check_v6')) {
    require_once __DIR__.'/activate.php';
}
if ((new GL_Plugin_Check_v6(__FILE__))->canProceed()) {
    require_once __DIR__.'/autoload.php';
    require_once __DIR__.'/compatibility.php';
    require_once __DIR__.'/deprecated.php';
    require_once __DIR__.'/helpers.php';
    require_once __DIR__.'/migration.php';
    $app = GeminiLabs\SiteReviews\Application::load();
    $app->make('Provider')->register($app);
    register_deactivation_hook(__FILE__, [$app, 'deactivate']);
    register_shutdown_function([$app, 'catchFatalError']);
    $app->init();
}
