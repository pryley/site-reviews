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
        $tests_dir = rtrim(sys_get_temp_dir(), '/\\').'/wordpress-tests-lib';
    }
    define('WP_TESTS_DIR', $tests_dir);
}

require_once WP_TESTS_DIR.'/includes/functions.php';

tests_add_filter('muplugins_loaded', function () use ($plugin_dir) {
    define('GLSR_UNIT_TESTS', true);
    require WP_CONTENT_DIR.'/plugins/elementor/elementor.php';
    require WP_CONTENT_DIR.'/plugins/woocommerce/woocommerce.php';
    require $plugin_dir.'/site-reviews.php';
});

tests_add_filter('setup_theme', function () use ($plugin_dir) {
    // Clean existing WC install first.
    define('WP_UNINSTALL_PLUGIN', true);
    define('WC_REMOVE_ALL_DATA', true);
    include WP_CONTENT_DIR.'/plugins/woocommerce/uninstall.php';
    require $plugin_dir.'/uninstall.php';
    WC_Install::install();
    glsr(Install::class)->run();
});

tests_add_filter('woocommerce_set_cookie_enabled', '__return_false');

require_once WP_TESTS_DIR.'/includes/bootstrap.php';
