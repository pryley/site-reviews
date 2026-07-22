<?php

/**
 * Plugin Name: Site Reviews Test Environment
 * Description: Loads the integration stubs and disables the deprecated fallbacks for the Pest suite.
 *
 * Must be an mu-plugin: deprecated.php registers its fallbacks on `plugins_loaded`, so the filters
 * that disable them must exist before the plugins load — too early for bootstrap.php, which only
 * gets control once wp-load.php returns. GLSR_UNIT_TESTS (defined by bootstrap.php before it
 * requires wp-load) keeps this inert for ordinary web requests; only the Pest process gets the stubs.
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
    // Some stubs extend a wp-admin class, which PHP needs at declaration time. wp-settings.php has
    // loaded wp-includes but nothing from wp-admin, and the plugin's autoloader does not exist yet
    // (plugins load after mu-plugins), so load the parents by hand:
    //
    //   WP_List_Table              profilepress, surecart
    //   Walker_Category_Checklist  multilingualpress
    //   Plugin_Upgrader            elementor (also declares WP_Upgrader_Skin, which elementor extends:
    //                              class-wp-upgrader.php requires every skin and WP_Upgrader subclass)
    //   WP_Importer                profilepress
    //
    // The others are single-class files extending wp-includes, so requiring them pulls in nothing else.
    $adminClasses = [
        'WP_List_Table' => 'class-wp-list-table.php',
        'Walker_Category_Checklist' => 'class-walker-category-checklist.php',
        'Plugin_Upgrader' => 'class-wp-upgrader.php',
        'WP_Importer' => 'class-wp-importer.php',
    ];
    foreach ($adminClasses as $adminClass => $adminFile) {
        if (!class_exists($adminClass)) {
            require_once ABSPATH."wp-admin/includes/{$adminFile}";
        }
    }
    /*
     * Stubs not loaded in the ordinary pass, reason traced — do not add without one.
     *
     * action-scheduler  The plugin BUNDLES Action Scheduler (vendors/woocommerce/action-scheduler),
     *                   which declares `abstract class ActionScheduler`; so does the stub, and
     *                   redeclaring it is fatal. Can never be loaded.
     *
     * Two stubs that load cleanly only because the integration was hardened:
     *
     * lpfw              LPFW() returns null from a stub; LPFW\Hooks::isEnabled() reads the option
     *                   name defensively (a third party's return value was never ours to trust), so
     *                   the integration registers quietly and isEnabled() returns false.
     * multilingualpress version() calls resolve(PluginProperties::class), null from a stub, and
     *                   ->version() on null raises an \Error a `catch (\Exception)` misses. Both
     *                   guards in MultilingualPress\Hooks catch \Throwable, closing the gate
     *                   instead of dying.
     */
    $excludedStubs = [
        'action-scheduler.php',
    ];
    /*
     * A stub for a plugin that is REALLY installed would redeclare it — fatal — so if .wp-env.json
     * installs one for real, its stub is dropped here. Nothing is installed for real today: the
     * suite is stubs only, and the integrations are excluded from the coverage gate precisely
     * because a stub cannot reach the half of an integration that reads a value back from the third
     * party. If you do install one, elementorpro must ride along with elementor: the pro stub
     * extends classes the free plugin declares, and a stub built against one Elementor version
     * cannot be trusted to extend another.
     */
    $realPlugins = [
        'woocommerce' => ['woocommerce.php'],
        'elementor' => ['elementor.php', 'elementorpro.php'],
    ];
    foreach ((array) get_option('active_plugins', []) as $activePlugin) {
        // Being listed in active_plugins is not the same as being loadable: removing a plugin from
        // .wp-env.json deletes its directory but leaves the option pointing at it, and WordPress
        // skips it silently. Dropping the stub on that entry would leave the symbols declared by
        // nobody, so the file must exist before its stub gives way.
        if (!file_exists(WP_PLUGIN_DIR.'/'.$activePlugin)) {
            continue;
        }
        $slug = strtok((string) $activePlugin, '/');
        foreach ($realPlugins[$slug] ?? [] as $stub) {
            $excludedStubs[] = $stub;
        }
    }
    $excludedStubs = array_unique($excludedStubs);
    /*
     * A stub whose classes extend another stub's must be required after it — PHP needs a parent at
     * declaration time, and nothing here autoloads. The generator permits such an edge: a parent
     * found in another stub counts as resolvable, since the files load together.
     *
     * Load order is therefore stated, not inferred. Alphabetical happens to satisfy both edges
     * today, but that is the alphabet's accident, not a guarantee.
     *
     *   elementor  elementorpro extends Elementor\Widget_Base and 188 others
     *   bricks     SureCart's 27 Bricks elements extend Bricks\Element
     */
    $stubPrerequisites = [
        'elementorpro.php' => ['elementor.php'],
        'surecart.php' => ['bricks.php'],
    ];
    $required = [];
    $requireStub = function (string $filename) use (&$requireStub, &$required, $stubPrerequisites, $excludedStubs, $stubsDir) {
        // The visited mark goes down before the recursion, so a cycle in the map stops here
        // instead of exhausting the stack.
        if (isset($required[$filename]) || in_array($filename, $excludedStubs)) {
            return;
        }
        $required[$filename] = true;
        if (!file_exists("{$stubsDir}/{$filename}")) {
            return;
        }
        foreach ($stubPrerequisites[$filename] ?? [] as $prerequisite) {
            $requireStub($prerequisite);
        }
        require_once "{$stubsDir}/{$filename}";
    };
    // glob() sorts; DirectoryIterator yields readdir order, which is the filesystem's own and is
    // hashed on ext4 — the order a laptop sees is not the order CI sees.
    foreach (glob("{$stubsDir}/*.php") ?: [] as $stubFile) {
        $requireStub(basename($stubFile));
    }
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');
    // remove deprecated fallbacks
    add_filter('site-reviews/support/deprecated/v5', '__return_false');
    add_filter('site-reviews/support/deprecated/v6', '__return_false');
    add_filter('site-reviews/support/deprecated/v7', '__return_false');
    add_filter('site-reviews/support/deprecated/v8', '__return_false');
}, 0);
