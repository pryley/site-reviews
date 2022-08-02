<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$plugin_dir = dirname(__DIR__);
$wordpress_root = $_SERVER['HOME'].'/Sites/wordpress';

if (getenv('WP_CONTENT_DIR') === false) {
    if (!file_exists($content_dir = $wordpress_root.'/public/app')) {
        $content_dir = '/tmp/wordpress/wp-content';
    }
    define('WP_CONTENT_DIR', $content_dir);
}
if (getenv('WP_PLUGIN_DIR') !== false) {
    define('WP_PLUGIN_DIR', getenv('WP_PLUGIN_DIR'));
}
if (getenv('WP_TESTS_DIR') === false) {
    if (!file_exists($tests_dir = $wordpress_root.'/tests/current')) {
        $tests_dir = '/tmp/wordpress-tests-lib';
    }
    define('WP_TESTS_DIR', $tests_dir);
}

require_once $plugin_dir.'/vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';
require_once WP_TESTS_DIR.'/includes/functions.php';

tests_add_filter('muplugins_loaded', function () use ($plugin_dir) {
    define('GLSR_UNIT_TESTS', true);
    require $plugin_dir.'/site-reviews.php';
    require WP_CONTENT_DIR.'/plugins/elementor/elementor.php';
});

tests_add_filter('setup_theme', function () use ($plugin_dir) {
    define('WP_UNINSTALL_PLUGIN', true);
    require $plugin_dir.'/uninstall.php';
});

require_once WP_TESTS_DIR.'/includes/bootstrap.php';
