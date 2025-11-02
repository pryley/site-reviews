<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

$plugin_dir = dirname(dirname(__DIR__));

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
    require_once $plugin_dir.'/site-reviews.php';
    $excludedStubs = [
        'action-scheduler.php',
        'elementorpro.php',
        'lpfw.php',
        'multilingualpress.php',
    ];
    $iterator = new \DirectoryIterator("{$plugin_dir}/tests/phpstan/stubs");
    foreach ($iterator as $fileinfo) {
        if (!$fileinfo->isFile()) {
            continue;
        }
        if (in_array($fileinfo->getFilename(), $excludedStubs)) {
            continue;
        }
        require_once $fileinfo->getPathname();
    }
    require_once $plugin_dir.'/tests/phpstan/stubs/elementorpro.php'; // fixes invalid order loading
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');
});

tests_add_filter('setup_theme', function () use ($plugin_dir) {
    define('WP_UNINSTALL_PLUGIN', true);
    require_once $plugin_dir.'/uninstall.php';
    glsr(Install::class)->run();
});

require_once WP_TESTS_DIR.'/includes/bootstrap.php';
