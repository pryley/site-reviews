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
        if (file_exists("{$pluginDir}/site-reviews.php") && is_dir("{$pluginDir}/tests/stubs")) {
            $stubsDir = "{$pluginDir}/tests/stubs";
            break;
        }
    }
    if ('' === $stubsDir) {
        fwrite(STDOUT, 'Site Reviews (with its tests/stubs) not found in '.WP_PLUGIN_DIR.PHP_EOL);
        exit(1);
    }
    // Some stubs declare a class that extends a wp-admin class, and PHP needs the
    // parent at declaration time. By now wp-settings.php has loaded the whole of
    // wp-includes but nothing at all from wp-admin, and the plugin's autoloader —
    // whose classmap would resolve these — does not exist yet either, because
    // plugins load after mu-plugins. So the parents are loaded by hand:
    //
    //   WP_List_Table              profilepress, surecart
    //   Walker_Category_Checklist  multilingualpress
    //
    // Both are single-class files that extend something from wp-includes, so
    // requiring them pulls in nothing else.
    $adminClasses = [
        'WP_List_Table' => 'class-wp-list-table.php',
        'Walker_Category_Checklist' => 'class-walker-category-checklist.php',
    ];
    foreach ($adminClasses as $adminClass => $adminFile) {
        if (!class_exists($adminClass)) {
            require_once ABSPATH."wp-admin/includes/{$adminFile}";
        }
    }
    /*
     * The stubs that are not loaded in the ordinary pass, and why. Both reasons
     * are traced — do not add to this list without one.
     *
     * action-scheduler  The plugin BUNDLES Action Scheduler (vendors/woocommerce/
     *                   action-scheduler), which declares `abstract class
     *                   ActionScheduler`, and so does the stub. Redeclaring it is
     *                   a fatal. This one can never be loaded.
     *
     * elementorpro      Not excluded, only deferred: it is required last, below,
     *                   because it extends classes the elementor stub declares.
     *
     * Two others used to be here and are now loaded:
     *
     * lpfw              LPFW() returns null from a stub, so LPFW\Hooks::isEnabled()
     *                   reads LPFW()->Plugin_Constants->EARN_ACTION_PRODUCT_REVIEW
     *                   and emits two "property on null" warnings on every boot.
     *                   That is noise, not a crash — the integration registers, and
     *                   isEnabled() correctly comes back false.
     *
     * multilingualpress This one WAS a fatal, and the fatal was ours: version()
     *                   calls resolve(PluginProperties::class), which a stub returns
     *                   null for, and ->version() on null raises an \Error — which
     *                   the `catch (\Exception)` around it could not catch. Both
     *                   guards in MultilingualPress\Hooks now catch \Throwable, so
     *                   an unreadable version is treated as an unsupported one and
     *                   the integration closes its own gate instead of dying.
     */
    $excludedStubs = [
        'action-scheduler.php',
        'elementorpro.php',
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
