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
    $file = glsr()->path('tests/pest/fixtures/updater/site-reviews-alerts.php');
    if ('0.9.0' !== $version) {
        $copy = get_temp_dir().'site-reviews-alerts-'.$version.'.php';
        file_put_contents($copy, str_replace('0.9.0', $version, (string) file_get_contents($file)));

        return $copy;
    }

    return $file;
}

test('a licensed addon answers the update filter with a real update entry', function () {
    $asked = licenseServer(['get_version' => updaterFixture('get-version-valid')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($asked->getArrayCopy())->toContain('get_version')
        ->and($update['version'])->toBe('1.0.0-beta1')
        ->and($update['package'])->not->toBe('')
        ->and($update['slug'])->toBe('site-reviews-alerts');
});

test('a server answer with no version keeps whatever wordpress already had', function () {
    licenseServer(['get_version' => []]);

    expect(glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]))->toBeFalse();
});

test('a compat addon behind the captured version is offered the update', function () {
    licenseServer(['get_version' => updaterFixture('get-version-valid')]);
    $file = updaterStubFile('0.9.0'); // 0.9.0 < 1.0.0-beta1
    glsr()->append('compat', $file, 'site-reviews-alerts');
    try {
        $updates = glsr(UpdateController::class)->filterUpdatePluginsTransient(
            (object) ['response' => [], 'no_update' => [], 'checked' => []]
        );
    } finally {
        glsr()->discard('compat');
    }

    $plugin = plugin_basename($file);
    expect($updates->response)->toHaveKey($plugin)
        ->and($updates->response[$plugin]->new_version)->toBe('1.0.0-beta1')
        ->and($updates->response[$plugin]->plugin)->toBe($plugin)
        ->and($updates->no_update)->toBe([])
        ->and($updates->checked[$plugin])->toBe('0.9.0');
});

test('a compat addon at or past the captured version is filed under no-update', function () {
    licenseServer(['get_version' => updaterFixture('get-version-valid')]);
    $file = updaterStubFile('1.0.0'); // 1.0.0 > 1.0.0-beta1: a beta is below its release
    glsr()->append('compat', $file, 'site-reviews-alerts');
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
    glsr()->append('compat', updaterStubFile('0.9.0'), 'site-reviews-alerts');
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
    // so under this contract it cannot fire. This is what the live server
    // answers until its edd_sl_license_response filter deploys (see the
    // backlog); the tests after the no-licence one cover the answer it gives
    // after that.
    licenseServer(['get_version' => updaterFixture('get-version-invalid')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
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

/*
 * The contract after the server-side fix. The server's edd_sl_license_response filter
 * withholds `package` and `download_link` unless check_license() answers `valid`, and
 * adds `license_status` — check_license()'s own vocabulary, or `missing` — plus
 * `license_renewal_url` alongside `expired`.
 *
 * These answers are DERIVED from the captured wrong-key one, not captured: the filter
 * is not deployed yet. Re-capture and replace them when it is (see the backlog).
 */

function withheldPackage(string $status, array $extra = []): array
{
    return array_merge(updaterFixture('get-version-invalid'), [
        'download_link' => '',
        'license_status' => $status,
        'package' => '',
    ], $extra);
}

test('a server that withholds the package says why, and the plugin repeats it', function () {
    licenseServer(['get_version' => withheldPackage('expired', [
        'license_renewal_url' => 'https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED',
    ])]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    // the Updates screen: the upgrade notice
    expect($update['package'])->toBe('')
        ->and($update['license_status'])->toBe('expired')
        ->and($update['license_renewal_url'])->toBe('https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED')
        ->and($update['upgrade_notice'])->toBe('‼️ Your license has expired. Renew it to update this plugin.');

    // the Plugins screen: the row, with the renewal link the server sent
    ob_start();
    glsr(UpdateController::class)->renderPluginUpdateMessage([], (object) $update);
    $row = (string) ob_get_clean();

    expect($row)->toContain('Your license has expired.')
        ->and($row)->toContain('<a href="https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED">Renew it</a>');
});

test('a release note no longer hides the licence message', function () {
    // The Updates screen shows one upgrade notice. Before, a release that carried one
    // replaced the licence message with it; now the licence comes first and the note follows.
    licenseServer(['get_version' => withheldPackage('missing', [
        'upgrade_notice' => '<p>Requires Site Reviews 8.0</p>',
    ])]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($update['upgrade_notice'])->toBe('‼️ Enter your license key to update this plugin. Requires Site Reviews 8.0');
});

test('a status the plugin does not know reads as the generic message', function () {
    licenseServer(['get_version' => withheldPackage('something_new')]);

    $update = glsr(UpdateController::class)->filterUpdatePlugins(false, [
        'TextDomain' => 'site-reviews-alerts',
        'UpdateURI' => 'https://niftyplugins.com',
    ]);

    expect($update['license_status'])->toBe('something_new') // kept, so a newer plugin can read it
        ->and($update['upgrade_notice'])->toBe('‼️ A valid license key is required to update this plugin.');
});

test('the failed auto-update email says which licence stopped the update, and where to fix it', function () {
    // WordPress attempts an automatic update whenever it is enabled for the plugin, and
    // its failure email names the plugin and the versions but not the reason. The reason
    // is on the update object the plugin built — the one WordPress hands back as `item`.
    $controller = glsr(UpdateController::class);
    $email = [
        'to' => 'admin@example.org',
        'subject' => '[Example] Some plugins have failed to update',
        'body' => "Howdy! Plugins failed to update on your site.\n\nThanks.",
        'headers' => '',
    ];
    $ours = (object) [
        'item' => (object) [
            'id' => 'https://niftyplugins.com', // WordPress copies the Update URI header here
            'license_renewal_url' => 'https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED',
            'license_status' => 'expired',
            'package' => '',
            'plugin' => 'site-reviews-alerts/site-reviews-alerts.php',
        ],
        'name' => 'Review Alerts',
        'result' => new WP_Error('no_package', 'Package not available.'),
    ];
    $licensed = (object) [ // one of ours that failed for some other reason
        'item' => (object) [
            'id' => 'https://niftyplugins.com',
            'package' => 'https://niftyplugins.com/edd-sl/package_download/SCRUBBED',
            'plugin' => 'site-reviews-forms/site-reviews-forms.php',
        ],
        'name' => 'Review Forms',
        'result' => new WP_Error('download_failed', 'Download failed.'),
    ];
    $theirs = (object) [ // not ours at all
        'item' => (object) [
            'id' => 'w.org/plugins/akismet',
            'package' => '',
            'plugin' => 'akismet/akismet.php',
        ],
        'name' => 'Akismet',
        'result' => new WP_Error('no_package', 'Package not available.'),
    ];

    $sent = $controller->filterAutoUpdateEmail($email, 'fail', [], ['plugin' => [$ours, $licensed, $theirs]]);

    expect($sent['body'])->toStartWith($email['body'])
        ->and($sent['body'])->toContain('The following Site Reviews addons did not update because their license does not allow it:')
        ->and($sent['body'])->toContain("\n- Review Alerts: Your license has expired. Renew it to update this plugin. https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED\n")
        ->and($sent['body'])->not->toContain('Review Forms')
        ->and($sent['body'])->not->toContain('Akismet')
        ->and($sent['subject'])->toBe($email['subject']);

    // a successful run, or a failure that is not about a licence, leaves the email alone
    expect($controller->filterAutoUpdateEmail($email, 'success', ['plugin' => [$ours]], []))->toBe($email)
        ->and($controller->filterAutoUpdateEmail($email, 'fail', [], ['plugin' => [$licensed, $theirs]]))->toBe($email)
        // and whatever another filter made of it is handed back untouched
        ->and($controller->filterAutoUpdateEmail(false, 'fail', [], ['plugin' => [$ours]]))->toBeFalse();
});
