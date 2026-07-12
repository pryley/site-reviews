<?php

/**
 * Plugin Name: Site Reviews Test Environment
 * Description: Loads the integration stubs and disables the deprecated fallbacks for the Pest suite.
 *
 * It has to be an mu-plugin: deprecated.php registers its fallbacks on
 * `plugins_loaded`, so the filters that disable them must exist before
 * WordPress loads the plugins — which is too early for tests/pest/bootstrap.php
 * (it only gets control once wp-load.php returns).
 *
 * GLSR_UNIT_TESTS is defined by tests/pest/bootstrap.php before it requires
 * wp-load.php, so this file is inert for ordinary web requests to the same
 * install — only the Pest process gets the stubs.
 */

defined('ABSPATH') || exit;

if (!defined('GLSR_UNIT_TESTS')) {
    return; // an ordinary web request to the same install
}

define('GLSR_TEST_MU_PLUGIN', true);

add_action('muplugins_loaded', function () {
    $stubsDir = '';
    $pluginDirs = [WP_PLUGIN_DIR.'/site-reviews']; // the usual folder name
    $pluginDirs = array_merge($pluginDirs, glob(WP_PLUGIN_DIR.'/*', GLOB_ONLYDIR) ?: []);
    foreach ($pluginDirs as $pluginDir) {
        if (file_exists("{$pluginDir}/site-reviews.php") && is_dir("{$pluginDir}/tests/phpstan/stubs")) {
            $stubsDir = "{$pluginDir}/tests/phpstan/stubs";
            break;
        }
    }
    if ('' === $stubsDir) {
        fwrite(STDOUT, 'Site Reviews (with its tests/phpstan/stubs) not found in '.WP_PLUGIN_DIR.PHP_EOL);
        exit(1);
    }
    // Two stubs (profilepress, surecart) declare a class that extends
    // \WP_List_Table, which is a wp-admin class: wp-settings.php has loaded all
    // of wp-includes by now, but nothing from wp-admin, and the plugin's
    // autoloader — whose classmap would resolve it — does not exist yet either
    // (plugins load after mu-plugins). PHP needs the parent at declaration
    // time, so it has to be here.
    if (!class_exists('WP_List_Table')) {
        require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
    }
    $excludedStubs = [
        'action-scheduler.php',
        'elementorpro.php',
        'lpfw.php',
        'multilingualpress.php',
    ];
    foreach (new \DirectoryIterator($stubsDir) as $fileinfo) {
        if (!$fileinfo->isFile() || 'php' !== $fileinfo->getExtension()) {
            continue; // never try to require a stray .DS_Store
        }
        if (in_array($fileinfo->getFilename(), $excludedStubs)) {
            continue;
        }
        require_once $fileinfo->getPathname();
    }
    require_once "{$stubsDir}/elementorpro.php"; // fixes invalid order loading
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');
    // remove deprecated fallbacks
    add_filter('site-reviews/support/deprecated/v5', '__return_false');
    add_filter('site-reviews/support/deprecated/v6', '__return_false');
    add_filter('site-reviews/support/deprecated/v7', '__return_false');
    add_filter('site-reviews/support/deprecated/v8', '__return_false');
}, 0);
