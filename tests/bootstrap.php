<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

define('WP_DEBUG', false); // Otherwise tests fail on PHP 8.1 due to deprecation notices

$plugin_dir = dirname(__DIR__);

if (getenv('WP_CONTENT_DIR') !== false) {
    define('WP_CONTENT_DIR', getenv('WP_CONTENT_DIR'));
}
if (getenv('WP_PLUGIN_DIR') !== false) {
    define('WP_PLUGIN_DIR', getenv('WP_PLUGIN_DIR'));
}
if (getenv('WP_TESTS_DIR') === false) {
    if (!file_exists($tests_dir = $_SERVER['HOME'].'/Sites/wordpress/tests/current')) {
        $tests_dir = rtrim(sys_get_temp_dir(), '/\\').'/wordpress-tests-lib';
    }
    define('WP_TESTS_DIR', $tests_dir);
}

require_once WP_TESTS_DIR.'/includes/functions.php';

tests_add_filter('muplugins_loaded', function () use ($plugin_dir) {
    define('GLSR_UNIT_TESTS', true);
    require $plugin_dir.'/site-reviews.php';
    require $plugin_dir.'/tests/phpstan/stubs/elementor.php';
    require $plugin_dir.'/tests/phpstan/stubs/elementor-pro.php';
    require $plugin_dir.'/tests/phpstan/stubs/mycred.php';
    require $plugin_dir.'/tests/phpstan/stubs/woocommerce.php';
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');
});

tests_add_filter('setup_theme', function () use ($plugin_dir) {
    define('WP_UNINSTALL_PLUGIN', true);
    require $plugin_dir.'/uninstall.php';
    glsr(Install::class)->run();
});

require_once WP_TESTS_DIR.'/includes/bootstrap.php';
