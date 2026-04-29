<?php
/**
 * ╔═╗╔═╗╔╦╗╦╔╗╔╦  ╦  ╔═╗╔╗ ╔═╗
 * ║ ╦║╣ ║║║║║║║║  ║  ╠═╣╠╩╗╚═╗
 * ╚═╝╚═╝╩ ╩╩╝╚╝╩  ╩═╝╩ ╩╚═╝╚═╝.
 *
 * Plugin Name:          Site Reviews
 * Plugin URI:           https://wordpress.org/plugins/site-reviews
 * Description:          Receive and display reviews on your website
 * Version:              8.0.10
 * Author:               Paul Ryley
 * Author URI:           https://site-reviews.com
 * License:              GPL3
 * License URI:          https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least:    6.7
 * Requires PHP:         8.1.2
 * Text Domain:          site-reviews
 * Domain Path:          languages
 * WC requires at least: 9.6
 * WC tested up to:      10.0
 */
defined('ABSPATH') || exit;

require_once __DIR__.'/autoload.php';
require_once __DIR__.'/compatibility.php';
require_once __DIR__.'/deprecated.php';
require_once __DIR__.'/helpers.php';
require_once __DIR__.'/migration.php';

$app = GeminiLabs\SiteReviews\Application::load();
$app->make('Provider')->register($app);
$app->init();

new GeminiLabs\SiteReviews\CLI();

register_shutdown_function([$app, 'catchFatalError']);
