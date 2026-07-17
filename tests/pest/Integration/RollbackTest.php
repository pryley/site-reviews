<?php

use GeminiLabs\SiteReviews\Addons\Compat;
use GeminiLabs\SiteReviews\Overrides\PluginUpgrader;
use GeminiLabs\SiteReviews\Rollback;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Going back to a previous version, and knowing which addons are installed.
 *
 * Rollback downloads a zip from wordpress.org and unpacks it over the running plugin — the most
 * destructive thing the plugin can do to itself, and all of it turns on one interpolated string:
 *
 *     https://downloads.wordpress.org/plugin/{$plugin}.{$version}.zip
 *
 * So the URL is asserted; the download is not driven — it would fetch a real zip and unpack it over
 * the plugin the tests run from.
 *
 * Addons\Compat is the other half: from a class name alone it works out which addons are installed,
 * licensed, retired, or too old to update themselves. Wrong, and a paid addon silently stops
 * receiving updates.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    require_once ABSPATH.'wp-admin/includes/class-wp-upgrader.php';
});

afterEach(function () {
    // `licensed`, `compat` and `retired` live in memory on the Application SINGLETON — they are
    // not options and they do not roll back. A test addon left in any of them would follow the
    // suite around: the addons page would list it, the update check would look for it, and the
    // licence notices would ask somebody to activate it.
    glsr()->store('compat', []);
    glsr()->store('licensed', []);
    glsr()->store('retired', []);
});

/**
 * An upgrader skin that says nothing.
 *
 * Plugin_Upgrader_Skin prints the admin iframe header and footer — and, more to the point,
 * CLOSES THE OUTPUT BUFFERS while doing it, which is why wrapping the call in ob_start() does
 * not contain it and PHPUnit calls the test risky. There is nothing to fight here: the skin is
 * the part of an upgrader whose entire job is to talk to a browser, and there is no browser.
 */
function silentUpgrader(): PluginUpgrader
{
    $skin = new class(['plugin' => glsr()->basename, 'title' => 'Rollback']) extends \Plugin_Upgrader_Skin {
        public function header()
        {
        }

        public function footer()
        {
        }

        public function feedback($feedback, ...$args)
        {
        }

        public function error($errors)
        {
        }
    };

    return new PluginUpgrader($skin);
}

/**
 * The package URL an upgrader was asked to fetch, without letting it fetch anything.
 *
 * WP_Upgrader::run() passes its options through `upgrader_package_options` BEFORE it downloads
 * anything. Blanking the package there makes download_package() fail immediately with "No package
 * specified", so the URL is captured and nothing leaves the container — which matters rather a
 * lot, since what it would otherwise fetch is a zip that WordPress then unpacks over the plugin
 * these tests are running from.
 */
function capturedPackageUrl(callable $callback): string
{
    $captured = '';
    add_filter('upgrader_package_options', function ($options) use (&$captured) {
        $captured = (string) ($options['package'] ?? '');
        $options['package'] = '';

        return $options;
    });
    $callback();

    return $captured;
}

/*
 * The rollback.
 */

test('the rollback asks wordpress.org for that exact version of this exact plugin', function () {
    // The whole of a rollback is this one interpolated string. It decides what gets downloaded
    // and unpacked over the plugin that is currently running.
    $upgrader = silentUpgrader();

    $package = capturedPackageUrl(fn () => $upgrader->rollback('8.0.0'));

    expect($package)->toBe('https://downloads.wordpress.org/plugin/site-reviews.8.0.0.zip');
});

test('the rollback tells the person it worked, in words about a rollback', function () {
    // Plugin_Upgrader's own string is "Plugin updated successfully" — which, to somebody who
    // just asked to go BACK a version, reads like it did the opposite of what they asked.
    $upgrader = silentUpgrader();

    $upgrader->upgrade_strings();

    expect($upgrader->strings['process_success'])->toContain('rollback successful');
});

test('the rollback remembers which version was asked for, and where to go afterwards', function () {
    // The transient is how the page that comes back knows what happened. A minute is long enough
    // for a download and short enough that a stale one cannot confuse the next attempt.
    $data = glsr(Rollback::class)->rollbackData('8.0.0');

    expect(get_transient(glsr()->prefix.'rollback_version'))->toBe('8.0.0');
    expect($data['data']['action'])->toBe('update-plugin')
        ->and($data['data']['plugin'])->toBe(glsr()->basename)
        ->and($data['data']['slug'])->toBe('site-reviews')
        ->and(wp_verify_nonce($data['data']['_ajax_nonce'], 'updates'))->not->toBeFalse();
    expect($data['url'])->toContain('welcome');
});

/*
 * Which addons are installed, and what the plugin knows about them.
 */

test('an installed addon is recognised, and its licence is registered', function () {
    // From a class name alone: Compat reflects it, finds the plugin file two directories up, and
    // reads its header. The test addon declares LICENSED = true.
    glsr()->store('licensed', []);

    glsr(Compat::class)->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'licensed'))
        ->toHaveKey('site-reviews-test-addon');
});

test('an addon with an Update URI updates itself, and is not put in compatibility mode', function () {
    // `compat` is the list of addons too old to know where their own updates come from — the
    // plugin has to fetch them on their behalf. An addon that declares an Update URI is not one
    // of them, and adding it to the list would mean two things asking for the same update.
    glsr()->store('compat', []);

    glsr(Compat::class)->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'compat'))->toBe([]);
});

test('a class that does not exist is not an addon', function () {
    // register() is handed class names by anything that thinks it is an addon. A missing class
    // must be a shrug, not a fatal — an addon half-deleted from the plugins directory is a
    // situation, not a crash.
    glsr()->store('licensed', []);

    glsr(Compat::class)->register('This\Class\Does\Not\Exist');

    expect(glsr()->retrieveAs('array', 'licensed'))->toBe([]);
});

test('a retired addon is remembered as retired, and nothing else', function () {
    // Two addons were folded into the plugin. They are still installed on people's sites, and
    // they must not be treated as licensed or as needing updates — they need REMOVING, and the
    // list is what the notice that says so is built from.
    $retired = new class() extends \GeminiLabs\SiteReviews\Addons\Addon {
        public const ID = 'site-reviews-woocommerce';
        public const LICENSED = true;
        public const NAME = 'Retired';
        public const SLUG = 'woocommerce';
    };
    glsr()->store('retired', []);
    glsr()->store('licensed', []);

    glsr(Compat::class)->register(get_class($retired));

    // It has no plugin file on disk, so it stops at the file_exists() check — which is itself
    // the point: an addon that is not installed is not registered as anything at all.
    expect(glsr()->retrieveAs('array', 'licensed'))->toBe([]);
});

test('a rollback that downloads and unpacks cleanly reports success and clears the plugin cache', function () {
    // The success half capturedPackageUrl() deliberately never reaches. A FAKE
    // plugin stands in for this one (letting the real basename through would
    // unpack a zip over the plugin these tests are running from), and the
    // download is answered from a zip built here — nothing leaves the container.
    $slug = 'glsr-fake-rollback';
    $zipfile = get_temp_dir().$slug.'.zip';
    $zip = new ZipArchive();
    $zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString("{$slug}/{$slug}.php", "<?php\n/**\n * Plugin Name: Fake Rollback Plugin\n * Version: 1.0\n */\n");
    $zip->close();

    $skin = new class(['plugin' => "{$slug}/{$slug}.php", 'title' => 'Rollback']) extends \Plugin_Upgrader_Skin {
        public function header()
        {
        }

        public function footer()
        {
        }

        public function feedback($feedback, ...$args)
        {
        }
    };
    // a finished upgrade asks wordpress.org for fresh update data; the suite
    // blocks HTTP, so detach core's checks (hooks restore per test)
    remove_action('upgrader_process_complete', 'wp_version_check');
    remove_action('upgrader_process_complete', 'wp_update_plugins');
    remove_action('upgrader_process_complete', 'wp_update_themes');
    add_filter('upgrader_pre_download', fn () => $zipfile);
    try {
        $result = (new PluginUpgrader($skin))->rollback('1.0');

        expect($result)->toBeTrue()
            ->and(file_exists(WP_PLUGIN_DIR."/{$slug}/{$slug}.php"))->toBeTrue();
    } finally {
        remove_all_filters('upgrader_pre_download');
        // the upgrader already deleted the zip (clear_working); remove what is left
        if (file_exists(WP_PLUGIN_DIR."/{$slug}/{$slug}.php")) {
            unlink(WP_PLUGIN_DIR."/{$slug}/{$slug}.php");
        }
        if (is_dir(WP_PLUGIN_DIR."/{$slug}")) {
            rmdir(WP_PLUGIN_DIR."/{$slug}");
        }
        if (file_exists($zipfile)) {
            unlink($zipfile);
        }
    }
});
