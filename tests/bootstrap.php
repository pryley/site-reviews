<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$plugin_dir = dirname(__DIR__);

if (!defined('WP_CONTENT_DIR') && getenv('WP_CONTENT_DIR') !== false) {
    define('WP_CONTENT_DIR', getenv('WP_CONTENT_DIR'));
}
if (!defined('WP_PLUGIN_DIR') && getenv('WP_PLUGIN_DIR') !== false) {
    define('WP_PLUGIN_DIR', getenv('WP_PLUGIN_DIR'));
}
if (!defined('WP_TESTS_DIR') && getenv('WP_TESTS_DIR') === false) {
    if (!file_exists($tests_dir = $_SERVER['HOME'].'/Sites/wordpress/tests/current')) {
        $tests_dir = rtrim(sys_get_temp_dir(), '/\\').'/wordpress-tests-lib';
    }
    define('WP_TESTS_DIR', $tests_dir);
}

require_once WP_TESTS_DIR.'/includes/functions.php';

tests_add_filter('muplugins_loaded', function () use ($plugin_dir) {
    define('GLSR_UNIT_TESTS', true);
    require $plugin_dir.'/site-reviews.php';
    // require $plugin_dir.'/tests/phpstan/stubs/akismet.php';
    require $plugin_dir.'/tests/phpstan/stubs/elementor.php';
    require $plugin_dir.'/tests/phpstan/stubs/elementorpro.php';
    // require $plugin_dir.'/tests/phpstan/stubs/gamipress.php';
    // require $plugin_dir.'/tests/phpstan/stubs/lpfw.php';
    // require $plugin_dir.'/tests/phpstan/stubs/multilingualpress.php';
    require $plugin_dir.'/tests/phpstan/stubs/mycred.php';
    // require $plugin_dir.'/tests/phpstan/stubs/polylang.php';
    // require $plugin_dir.'/tests/phpstan/stubs/wlpr.php';
    require $plugin_dir.'/tests/phpstan/stubs/woocommerce.php';
    // require $plugin_dir.'/tests/phpstan/stubs/woorewards.php';
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
