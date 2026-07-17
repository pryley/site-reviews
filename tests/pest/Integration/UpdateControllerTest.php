<?php

use GeminiLabs\SiteReviews\Controllers\UpdateController;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Addon updates, which do not come from wordpress.org: the plugins_api details modal, and
 * the throttle that decides how often the licence server is asked at all.
 *
 * isAddon() insists on a real installed addon (a directory in WP_PLUGIN_DIR whose plugin
 * file declares the niftyplugins Update URI), so one is staged on disk for the duration —
 * the container's plugin directory, cleaned up in finally, never the repo.
 */

const FAKE_ADDON = 'site-reviews-fakeaddon';

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

function stageFakeAddon(): void
{
    $dir = WP_PLUGIN_DIR.'/'.FAKE_ADDON;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents("{$dir}/".FAKE_ADDON.'.php', implode("\n", [
        '<?php',
        '/**',
        ' * Plugin Name: Site Reviews: Fake Addon (test fixture)',
        ' * Version: 1.0.0',
        ' * Update URI: https://niftyplugins.com',
        ' */',
    ]));
}

function unstageFakeAddon(): void
{
    @unlink(WP_PLUGIN_DIR.'/'.FAKE_ADDON.'/'.FAKE_ADDON.'.php');
    @rmdir(WP_PLUGIN_DIR.'/'.FAKE_ADDON);
}

function fakeVersionResponse(): Closure
{
    $fake = fn () => [
        'body' => (string) wp_json_encode([
            'name' => 'Site Reviews: Fake Addon',
            'new_version' => '9.9.9',
            'slug' => FAKE_ADDON,
            'version' => '9.9.9',
        ]),
        'cookies' => [],
        'filename' => null,
        'headers' => [],
        'response' => ['code' => 200, 'message' => 'OK'],
    ];
    add_filter('pre_http_request', $fake);

    return $fake;
}

test('the plugin details modal is answered for an installed addon, and nobody else', function () {
    $controller = glsr(UpdateController::class);

    // not the details action, or no slug: pass through
    expect($controller->filterPluginsApi(false, 'query_plugins', (object) ['slug' => FAKE_ADDON]))->toBeFalse();
    expect($controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => '']))->toBeFalse();
    // a slug that is not even shaped like an addon
    expect($controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => 'akismet']))->toBeFalse();
    // shaped like one, but not installed
    expect($controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => 'site-reviews-notinstalled']))->toBeFalse();

    stageFakeAddon();
    $http = fakeVersionResponse();
    try {
        $details = $controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => FAKE_ADDON]);
    } finally {
        remove_filter('pre_http_request', $http);
        unstageFakeAddon();
    }

    expect($details)->toBeObject()
        ->and($details->version)->toBe('9.9.9');

    // asked twice, answered from the memo
    expect($controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => FAKE_ADDON.'-nope']))->toBeFalse();
    expect($controller->filterPluginsApi(false, 'plugin_information', (object) ['slug' => FAKE_ADDON.'-nope']))->toBeFalse();
});

test('an addon the licence server does not know keeps wordpress\'s own answer', function () {
    // A fresh slug (the version cache is per-addon) whose lookup returns nothing usable.
    $slug = 'site-reviews-unknownaddon';
    $dir = WP_PLUGIN_DIR.'/'.$slug;
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents("{$dir}/{$slug}.php", "<?php\n/**\n * Plugin Name: Unknown Addon\n * Update URI: https://niftyplugins.com\n */\n");
    $http = fn () => [
        'body' => '{}', 'cookies' => [], 'filename' => null, 'headers' => [],
        'response' => ['code' => 200, 'message' => 'OK'],
    ];
    add_filter('pre_http_request', $http);
    try {
        $data = glsr(UpdateController::class)->filterPluginsApi(false, 'plugin_information', (object) ['slug' => $slug]);
    } finally {
        remove_filter('pre_http_request', $http);
        @unlink("{$dir}/{$slug}.php");
        @rmdir($dir);
    }

    expect($data)->toBeFalse(); // no version in the answer: pass through untouched
});

test('the licence server is asked at a rate set by where the question comes from', function () {
    $controller = glsr(UpdateController::class);
    $expired = fn () => protectedMethod(UpdateController::class, 'hasTimeoutExpired')
        ->invoke($controller, FAKE_ADDON);
    $lastChecked = glsr()->prefix.'last_checked_'.FAKE_ADDON;

    // never asked before: expired, and the asking is recorded
    delete_site_option($lastChecked);
    expect($expired())->toBeTrue();

    // just asked: the default timeout is 12 hours, so not again now
    expect($expired())->toBeFalse();

    // doing_filter() reads the $wp_current_filter stack, so the contexts are staged by
    // pushing the hook name — firing the real hooks would run core's own listeners, which
    // are not all loaded in a CLI process.
    $within = function (string $hook, Closure $callback) {
        $GLOBALS['wp_current_filter'][] = $hook;
        try {
            return $callback();
        } finally {
            array_pop($GLOBALS['wp_current_filter']);
        }
    };

    // an update run always asks
    expect($within('upgrader_process_complete', $expired))->toBeTrue();

    // the update-core screen with force-check asks; without it, only after a minute
    update_site_option($lastChecked, time());
    $_GET['force-check'] = '1';
    expect($within('load-update-core.php', $expired))->toBeTrue();
    unset($_GET['force-check']);

    // the plugins screen waits an hour
    update_site_option($lastChecked, time() - (2 * MINUTE_IN_SECONDS));
    expect($within('load-plugins.php', $expired))->toBeFalse();

    // cron waits two
    update_site_option($lastChecked, time() - HOUR_IN_SECONDS);
    add_filter('wp_doing_cron', '__return_true');
    expect($expired())->toBeFalse();
    remove_filter('wp_doing_cron', '__return_true');
});

test('an update without a download package explains the licence', function () {
    ob_start();
    glsr(UpdateController::class)->renderPluginUpdateMessage(
        ['PluginURI' => 'https://niftyplugins.com/plugin/x'],
        (object) ['package' => '']
    );
    $message = (string) ob_get_clean();

    expect($message)->toContain('license key');

    ob_start();
    glsr(UpdateController::class)->renderPluginUpdateMessage(
        [], (object) ['package' => 'https://example.org/x.zip']
    );
    expect((string) ob_get_clean())->toBe(''); // a licensed update needs no lecture
});

test('an empty update transient is handed back before any addon work', function () {
    expect(glsr(UpdateController::class)->filterUpdatePluginsTransient(false))->toBeFalse()
        ->and(glsr(UpdateController::class)->filterUpdatePluginsTransient(null))->toBeNull();
});

/*
 * The captured contract. The fixtures under tests/pest/fixtures/updater/ are
 * real niftyplugins.com responses (captured 2026-07-17, scrubbed — see the
 * README there); these tests pin the filters against them rather than against
 * an invented payload.
 */

function updaterFixture(string $name): array
{
    $path = glsr()->path("tests/pest/fixtures/updater/{$name}.json");

    return (array) json_decode((string) file_get_contents($path), true);
}

function updaterStubFile(string $version = '0.9.0'): string
{
    // the local half of the check: version_compare runs against this header
    $file = glsr()->path('tests/pest/fixtures/updater/site-reviews-actions.php');
    if ('0.9.0' !== $version) {
        $copy = get_temp_dir().'site-reviews-actions-'.$version.'.php';
        file_put_contents($copy, str_replace('0.9.0', $version, (string) file_get_contents($file)));

        return $copy;
    }

    return $file;
}

test('a licensed addon answers the update filter with a real update entry', function () {
    $asked = licenseServer(['get_version' => updaterFixture('get-version-valid')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-actions',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($asked->getArrayCopy())->toContain('get_version')
        ->and($update['version'])->toBe('1.0.0-beta12')
        ->and($update['package'])->not->toBe('')
        ->and($update['slug'])->toBe('site-reviews-actions');
});

test('a server answer with no version keeps whatever wordpress already had', function () {
    licenseServer(['get_version' => []]);

    expect(glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-actions',
        'UpdateURI' => 'https://niftyplugins.com',
    ]))->toBeFalse();
});

test('a compat addon behind the captured version is offered the update', function () {
    licenseServer(['get_version' => updaterFixture('get-version-valid')]);
    $file = updaterStubFile('0.9.0'); // 0.9.0 < 1.0.0-beta12
    glsr()->append('compat', $file, 'site-reviews-actions');
    try {
        $updates = glsr(UpdateController::class)->filterUpdatePluginsTransient(
            (object) ['response' => [], 'no_update' => [], 'checked' => []]
        );
    } finally {
        glsr()->discard('compat');
    }

    $plugin = plugin_basename($file);
    expect($updates->response)->toHaveKey($plugin)
        ->and($updates->response[$plugin]->new_version)->toBe('1.0.0-beta12')
        ->and($updates->response[$plugin]->plugin)->toBe($plugin)
        ->and($updates->no_update)->toBe([])
        ->and($updates->checked[$plugin])->toBe('0.9.0');
});

test('a compat addon at or past the captured version is filed under no-update', function () {
    licenseServer(['get_version' => updaterFixture('get-version-valid')]);
    $file = updaterStubFile('1.0.0'); // 1.0.0 > 1.0.0-beta12: a beta is below its release
    glsr()->append('compat', $file, 'site-reviews-actions');
    try {
        $updates = glsr(UpdateController::class)->filterUpdatePluginsTransient(
            (object) ['response' => [], 'no_update' => [], 'checked' => []]
        );
    } finally {
        glsr()->discard('compat');
        @unlink($file);
    }

    $plugin = plugin_basename($file);
    expect($updates->no_update)->toHaveKey($plugin)
        ->and($updates->response)->toBe([])
        ->and($updates->checked[$plugin])->toBe('1.0.0');
});

test('a compat addon the server does not answer is left alone', function () {
    licenseServer(['get_version' => []]);
    glsr()->append('compat', updaterStubFile('0.9.0'), 'site-reviews-actions');
    try {
        $updates = glsr(UpdateController::class)->filterUpdatePluginsTransient(
            (object) ['response' => [], 'no_update' => [], 'checked' => []]
        );
    } finally {
        glsr()->discard('compat');
    }

    expect($updates->response)->toBe([])
        ->and($updates->no_update)->toBe([])
        ->and($updates->checked)->toBe([]); // skipped entirely, not booked as checked
});

test('the captured contract: an invalid licence still answers a package', function () {
    // The server refuses only in `msg` (a field the Defaults drop) and the
    // package URL — which refuses at DOWNLOAD time. The plugin's "a valid
    // license key is required" message renders only when the package is empty,
    // so under this contract it cannot fire (see ROADMAP).
    licenseServer(['get_version' => updaterFixture('get-version-invalid')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-actions',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($update['package'])->not->toBe(''); // non-empty even unlicensed

    ob_start();
    glsr(UpdateController::class)->renderPluginUpdateMessage(
        [], (object) ['package' => $update['package']]
    );
    expect(ob_get_clean())->toBe(''); // and so the licence message never shows
});

test('the captured contract: a missing licence key gets the licence message', function () {
    // The live no-licence answer has an EMPTY package — so this is the case the
    // message machinery was built for, and here it genuinely fires: the
    // Defaults' finalize() writes the upgrade notice, and the row message
    // renders. (The wrong-key answer still carries a phantom package — the
    // ROADMAP note — so only THAT path stays dark.)
    licenseServer(['get_version' => updaterFixture('get-version-no-licence')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($update['package'])->toBe('')
        ->and($update['upgrade_notice'])->toContain('license key is required');

    ob_start();
    glsr(UpdateController::class)->renderPluginUpdateMessage(
        [], (object) ['package' => $update['package']]
    );
    expect(ob_get_clean())->toContain('license key');
});
