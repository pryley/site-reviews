<?php

use GeminiLabs\SiteReviews\Commands\ConnectPremium;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;
use function GeminiLabs\SiteReviews\Tests\restRequest;

/*
 * The routes of the License Key row (premium/license) and of the install (premium/connect).
 */

beforeEach(function () {
    resetPluginState();
    $GLOBALS['wp_rest_server'] = new WP_REST_Server();
    do_action('rest_api_init', $GLOBALS['wp_rest_server']);
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    unset($GLOBALS['wp_rest_server']);
});

function connectPremium(): WP_REST_Response
{
    return restRequest('POST', '/site-reviews/v1/premium/connect', []);
}

function deactivatePremium(bool $delete = false): WP_REST_Response
{
    return restRequest('DELETE', '/site-reviews/v1/premium/license', ['delete' => $delete]);
}

function verifyPremium(string $license = 'a-premium-key'): WP_REST_Response
{
    return restRequest('POST', '/site-reviews/v1/premium/license', ['license' => $license]);
}

function validPremiumServer(array $check = [], array $version = []): ArrayObject
{
    return licenseServer([
        'check_license' => array_replace([
            'success' => true,
            'license' => 'valid',
            'expires' => '2030-06-15 23:59:59',
            'connect_url' => 'https://niftyplugins.com/connect/',
        ], $check),
        'get_version' => array_replace([
            'requires' => '6.0',
            'requires_php' => '8.0',
            'package' => 'https://niftyplugins.com/edd-sl/package_download/a-token/',
        ], $version),
    ]);
}

function savedPremiumKey(): string
{
    return (string) glsr_get_option('licenses.site-reviews-premium');
}

/*
 * Who may call the row's route.
 */

test('a visitor is refused with a code the script reads as final', function () {
    wp_set_current_user(0);

    expect(verifyPremium()->get_data()['code'])->toBe('glsr_not_logged_in')
        ->and(verifyPremium()->get_status())->toBe(401)
        ->and(deactivatePremium()->get_data()['code'])->toBe('glsr_not_logged_in')
        ->and(connectPremium()->get_data()['code'])->toBe('glsr_not_logged_in');
});

test('a user who cannot save license keys is refused without asking the server', function () {
    wp_set_current_user(createUser(['role' => 'editor']));
    $asked = licenseServer([]);

    $response = verifyPremium();

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('glsr_cannot_manage')
        ->and(deactivatePremium()->get_data()['code'])->toBe('glsr_cannot_manage')
        ->and($asked)->toHaveCount(0);
});

test('an empty key is refused without asking the server', function () {
    $asked = licenseServer([]);

    $response = verifyPremium('');

    expect($response->get_status())->toBe(400)
        ->and($response->get_data()['code'])->toBe('glsr_license_missing')
        ->and($response->get_data()['type'])->toBe('warning')
        ->and($asked)->toHaveCount(0);
});

/*
 * Verify: the licence (failure rows 1 to 4).
 */

test('a key the server does not recognise is refused and not saved', function () {
    licenseServer(['check_license' => ['success' => false, 'license' => 'invalid']]);

    $response = verifyPremium('a-bad-key');

    expect($response->get_status())->toBe(400)
        ->and($response->get_data()['code'])->toBe('glsr_license_invalid')
        ->and($response->get_data()['link']['url'])->toContain('license-keys')
        ->and(savedPremiumKey())->toBe('');
});

test('a disabled key is told apart from an unknown one', function () {
    // The server answers disabled, with success true, for a key that exists but was revoked.
    licenseServer(['check_license' => ['success' => true, 'license' => 'disabled']]);

    $response = verifyPremium('a-disabled-key');

    expect($response->get_status())->toBe(400)
        ->and($response->get_data()['code'])->toBe('glsr_license_disabled')
        ->and($response->get_data()['message'])->toContain('has been disabled')
        ->and($response->get_data()['link']['url'])->toContain('account/support/')
        ->and(savedPremiumKey())->toBe('');
});

test('a key for an addon is told apart from an invalid one', function () {
    // The server answers item_name_mismatch with success true.
    licenseServer(['check_license' => ['success' => true, 'license' => 'item_name_mismatch']]);

    $response = verifyPremium('an-addon-key');

    expect($response->get_data()['code'])->toBe('glsr_license_mismatch')
        ->and($response->get_data()['message'])->toContain('different product')
        ->and(savedPremiumKey())->toBe('');
});

test('an expired key is saved, as the Licenses tab saves one, and still refused', function () {
    licenseServer(['check_license' => ['success' => true, 'license' => 'expired', 'expires' => '2020-01-01 23:59:59']]);

    $response = verifyPremium('an-expired-key');

    expect($response->get_data()['code'])->toBe('glsr_license_expired')
        ->and($response->get_data()['type'])->toBe('error')
        ->and($response->get_data()['link']['url'])->toContain('license-keys')
        ->and($response->get_data()['html'])->toContain('expired on January 1, 2020') // the row is redrawn with the saved key
        ->and(savedPremiumKey())->toBe('an-expired-key')
        ->and(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INACTIVE);
});

test('a key with no activations left is refused with the Manage Sites instruction', function () {
    licenseServer(['check_license' => ['success' => true, 'license' => 'site_inactive', 'license_limit' => 2, 'activations_left' => 0]]);

    $response = verifyPremium('a-used-up-key');

    expect($response->get_data()['code'])->toBe('glsr_license_inactive')
        ->and($response->get_data()['type'])->toBe('warning')
        ->and($response->get_data()['message'])->toContain('Manage Sites')
        ->and($response->get_data())->not->toHaveKey('html')
        ->and(savedPremiumKey())->toBe('');
});

test('a key not yet activated here is activated, saved, and the row redrawn as active', function () {
    // An unlimited licence: license_limit 0 and activations_left 'unlimited', which the int cast makes 0.
    $asked = licenseServer([
        'check_license' => ['success' => true, 'license' => 'inactive', 'license_limit' => 0, 'activations_left' => 'unlimited'],
        'activate_license' => ['success' => true, 'license' => 'valid', 'expires' => '2030-06-15 23:59:59'],
    ]);
    add_filter('pre_http_request', function ($pre, $args) use ($asked) {
        if ('check_license' === ($args['body']['edd_action'] ?? '') && in_array('activate_license', $asked->getArrayCopy(), true)) {
            return array_replace($pre, ['body' => wp_json_encode(['success' => true, 'license' => 'valid', 'expires' => '2030-06-15 23:59:59'])]);
        }
        return $pre;
    }, 11, 2); // after licenseServer(), which answers every request

    $response = verifyPremium('a-new-key');

    expect($response->get_status())->toBe(200)
        ->and(array_slice($asked->getArrayCopy(), 0, 2))->toBe(['check_license', 'activate_license'])
        ->and(savedPremiumKey())->toBe('a-new-key')
        ->and($response->get_data()['state'])->toBe(PremiumLicense::STATE_ACTIVE)
        ->and($response->get_data()['html'])->toContain('data-action="install"')
        ->and($response->get_data())->not->toHaveKey('connect'); // the server named no connect page
});

test('no answer from the server changes nothing and says to try again', function () {
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $response = verifyPremium('a-key');

    expect($response->get_status())->toBe(503)
        ->and($response->get_data()['code'])->toBe('glsr_license_server')
        ->and($response->get_data()['message'])->toContain('try again')
        ->and(savedPremiumKey())->toBe('');
});

test('a valid key is saved, the row comes back active, and the same click goes on to the store', function () {
    $asked = validPremiumServer();

    $response = verifyPremium();
    $data = $response->get_data();

    expect($response->get_status())->toBe(200)
        ->and($asked->getArrayCopy())->toBe(['check_license', 'get_version']) // one check: Install's own route checks again
        ->and(savedPremiumKey())->toBe('a-premium-key')
        ->and($data['state'])->toBe(PremiumLicense::STATE_ACTIVE)
        ->and($data['html'])->toContain('readonly')
        ->and($data['html'])->toContain('data-action="deactivate"')
        ->and($data['html'])->toContain('data-action="install"')
        ->and($data['html'])->toContain('expires on June 15, 2030')
        ->and($data['connect']['url'])->toBe('https://niftyplugins.com/connect/')
        ->and(array_keys($data['connect']['fields']))->toBe(['ajax', 'endpoint', 'license', 'return', 'site', 'token'])
        ->and(get_transient(ConnectPremium::tokenKey($data['connect']['fields']['token'])))->toBeArray()
        ->and($data)->not->toHaveKey('notice');
});

test('a valid key on a site that cannot install stays on the row, and a refused site check is shown under it', function () {
    add_filter('file_mod_allowed', '__return_false');
    validPremiumServer();
    $data = verifyPremium()->get_data();
    expect($data['state'])->toBe(PremiumLicense::STATE_ACTIVE)
        ->and($data)->not->toHaveKey('connect')
        ->and($data)->not->toHaveKey('notice');

    remove_filter('file_mod_allowed', '__return_false');
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    validPremiumServer([], ['requires' => '99.0']);
    $data = verifyPremium('another-key')->get_data();
    expect(savedPremiumKey())->toBe('another-key')
        ->and($data['state'])->toBe(PremiumLicense::STATE_ACTIVE)
        ->and($data)->not->toHaveKey('connect')
        ->and($data['notice']['code'])->toBe('glsr_wordpress_version')
        ->and($data['notice']['type'])->toBe('error');
});

/*
 * Deactivate and Delete.
 */

test('Deactivate frees the seat, keeps the key, and the row is inactive on the next load too', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    $asked = validPremiumServer();
    expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_ACTIVE); // cached for the day
    // Once deactivated the server calls the site inactive.
    add_filter('pre_http_request', function ($pre, $args) use ($asked) {
        if ('check_license' === ($args['body']['edd_action'] ?? '') && in_array('deactivate_license', $asked->getArrayCopy(), true)) {
            return array_replace($pre, ['body' => wp_json_encode(['success' => true, 'license' => 'site_inactive'])]);
        }
        return $pre;
    }, 11, 2); // after licenseServer(), which answers every request

    $response = deactivatePremium();

    expect($response->get_status())->toBe(200)
        ->and($asked->getArrayCopy())->toContain('deactivate_license')
        ->and(savedPremiumKey())->toBe('a-premium-key')
        ->and($response->get_data()['state'])->toBe(PremiumLicense::STATE_INACTIVE)
        ->and($response->get_data()['html'])->toContain('data-action="delete"')
        ->and(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INACTIVE); // not the day's cache
});

test('Delete clears the key without asking the server', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    $asked = licenseServer([]);

    $response = deactivatePremium(true);

    expect($response->get_status())->toBe(200)
        ->and($asked->getArrayCopy())->not->toContain('deactivate_license')
        ->and(savedPremiumKey())->toBe('')
        ->and($response->get_data()['html'])->not->toContain('data-action="delete"');
});

test('Deactivate with no saved key is refused', function () {
    expect(deactivatePremium()->get_data()['code'])->toBe('glsr_license_missing');
});

/*
 * Install (premium/connect): who may call it, and the licence again.
 */

test('a user who cannot install plugins is refused the install', function () {
    wp_set_current_user(createUser(['role' => 'editor']));
    $asked = licenseServer([]);

    $response = connectPremium();

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('glsr_cannot_install')
        ->and($asked)->toHaveCount(0);
});

test('a site that disallows file modifications is refused before the key is checked', function () {
    add_filter('file_mod_allowed', '__return_false');
    $asked = licenseServer([]);

    $response = connectPremium();

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('glsr_cannot_install')
        ->and($asked)->toHaveCount(0);
});

test('an install without a saved key is refused without asking the server', function () {
    $asked = licenseServer([]);

    $response = connectPremium();

    expect($response->get_status())->toBe(400)
        ->and($response->get_data()['code'])->toBe('glsr_license_missing')
        ->and($asked)->toHaveCount(0);
});

test('an install while the server is unreachable warns and leaves the row as it was', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_license_server')
        ->and($response->get_data()['type'])->toBe('warning')
        ->and($response->get_data())->not->toHaveKey('html');
});

test('a saved key the server has since refused is refused at the install too', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    licenseServer(['check_license' => ['success' => false, 'license' => 'invalid']]);

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_license_invalid')
        ->and($response->get_data()['type'])->toBe('error')
        ->and($response->get_data()['html'])->toContain('invalid license key for Site Reviews Premium'); // the row redrawn to what the server says now
});

/*
 * The site (failure rows 5, 6, 11 and 12). The key is saved by then.
 */

function savedKeyForInstall(array $check = [], array $version = []): ArrayObject
{
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    return validPremiumServer($check, $version);
}

test('a WordPress below what premium requires is refused, with the key saved', function () {
    savedKeyForInstall([], ['requires' => '99.0']);

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_wordpress_version')
        ->and($response->get_data()['message'])->toContain('99.0')
        ->and(savedPremiumKey())->toBe('a-premium-key');
});

test('a PHP below what premium requires is refused, with the key saved', function () {
    savedKeyForInstall([], ['requires_php' => '99.0']);

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_php_version')
        ->and(savedPremiumKey())->toBe('a-premium-key');
});

test('a site that needs FTP credentials to write files is sent to the manual install', function () {
    // wp-env defines FS_METHOD=direct; the filter is what decides after the constant.
    add_filter('filesystem_method', fn () => 'ftpext');
    savedKeyForInstall();

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_filesystem')
        ->and($response->get_data()['link']['url'])->toContain('/account/')
        ->and(savedPremiumKey())->toBe('a-premium-key');
});

test('a connect page the server did not name, or named elsewhere, is refused', function ($connectUrl) {
    savedKeyForInstall(['connect_url' => $connectUrl]);

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_connect_page')
        ->and(savedPremiumKey())->toBe('a-premium-key');
})->with([
    'none' => '',
    'http' => 'http://niftyplugins.com/connect/',
    'another host' => 'https://example.org/connect/',
    'a lookalike' => 'https://niftyplugins.com.example.org/connect/',
]);

test('a site whose admin address is on another host than its home is stopped on the page', function () {
    // The store would refuse a return address on another host than the licence URL's.
    add_filter('admin_url', fn ($url) => str_replace(wp_parse_url($url, PHP_URL_HOST), 'wp.example.org', $url));
    savedKeyForInstall();

    $response = connectPremium();

    expect($response->get_data()['code'])->toBe('glsr_site_addresses')
        ->and(savedPremiumKey())->toBe('a-premium-key');
});

/*
 * The hand-over.
 */

test('a verified key answers with the store page and the six fields the row posts to it', function () {
    $asked = savedKeyForInstall();

    $response = connectPremium();
    $data = $response->get_data();

    expect($response->get_status())->toBe(200)
        ->and($asked->getArrayCopy())->toBe(['check_license', 'get_version'])
        ->and($data['url'])->toBe('https://niftyplugins.com/connect/')
        ->and(array_keys($data['fields']))->toBe(['ajax', 'endpoint', 'license', 'return', 'site', 'token'])
        ->and($data['fields']['license'])->toBe('a-premium-key')
        ->and($data['fields']['site'])->toBe(GeminiLabs\SiteReviews\Helpers\Url::license())
        ->and($data['fields']['endpoint'])->toEndWith('/site-reviews/v1/premium/install')
        ->and($data['fields']['ajax'])->toEndWith('/wp-admin/admin-ajax.php')
        ->and($data['fields']['return'])->toContain('page=glsr-settings')
        ->and($data['fields']['return'])->toContain('tab=general')
        ->and(strlen($data['fields']['token']))->toBe(32)
        ->and(savedPremiumKey())->toBe('a-premium-key');
});

test('the token is kept as its hash, for the user who made it, and for a quarter of an hour', function () {
    savedKeyForInstall();

    $token = connectPremium()->get_data()['fields']['token'];
    $record = get_transient(ConnectPremium::tokenKey($token));

    expect($record['user_id'])->toBe(get_current_user_id())
        ->and($record['created'])->toBeGreaterThan(time() - 5)
        ->and(get_option('_transient_timeout_'.ConnectPremium::tokenKey($token)))->toBeGreaterThan(time() + 14 * MINUTE_IN_SECONDS)
        ->and(get_option('_transient_'.glsr()->prefix.'premium_token_'.$token))->toBeFalse(); // never the token itself
});

/*
 * premium/install: the store's call. Anonymous, authorised by the token alone.
 */

function premiumToken(): string
{
    $token = wp_generate_password(32, false);
    set_transient(ConnectPremium::tokenKey($token), ['created' => time(), 'user_id' => get_current_user_id()], MINUTE_IN_SECONDS);
    return $token;
}

function installPremium(?string $token = null): WP_REST_Response
{
    return restRequest('POST', '/site-reviews/v1/premium/install', ['token' => $token ?? premiumToken()]);
}

/**
 * A zip the way the licence server packages premium, served as the package download's answer.
 */
function premiumStubPackage(string $mainFile = 'site-reviews-premium.php'): string
{
    $path = get_temp_dir().'site-reviews-premium-stub-'.wp_generate_password(6, false).'.zip';
    $zip = new ZipArchive();
    $zip->open($path, ZipArchive::CREATE);
    $zip->addFromString("site-reviews-premium/{$mainFile}", "<?php\n/*\nPlugin Name: Site Reviews Premium (stub)\nVersion: 0.0.1\n*/\n");
    $zip->close();
    premiumPackageDownload(function ($args) use ($path) {
        copy($path, $args['filename']);
        return ['response' => ['code' => 200, 'message' => 'OK']];
    });
    return $path;
}

/**
 * What the package address answers. After licenseServer(), which answers every request.
 *
 * @param callable(array): (array|WP_Error) $answer given the request args
 */
function premiumPackageDownload(callable $answer): void
{
    add_filter('pre_http_request', function ($pre, $args, $url) use ($answer) {
        if (!str_contains((string) $url, 'package_download')) {
            return $pre;
        }
        $response = $answer($args);
        return is_wp_error($response) ? $response : array_replace($pre, $response);
    }, 12, 3);
}

function premiumPackageServer(string $package = 'https://niftyplugins.com/edd-sl/package_download/a-token/', array $version = []): ArrayObject
{
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    $asked = licenseServer(['get_version' => array_replace(['package' => $package, 'new_version' => '1.0.0'], $version)]);
    // After an install WordPress checks api.wordpress.org for updates (upgrader_process_complete).
    add_filter('pre_http_request', function ($pre, $args, $url) {
        if (str_contains((string) $url, 'niftyplugins.com')) {
            return $pre;
        }
        return array_replace($pre, ['body' => '{"offers":[],"plugins":{},"themes":{},"no_update":{},"translations":[]}']); // nothing to update
    }, 11, 3);
    return $asked;
}

function removePremiumStub(): void
{
    deactivate_plugins(GeminiLabs\SiteReviews\Commands\InstallPremium::PLUGIN_FILE, true);
    $dir = WP_PLUGIN_DIR.'/site-reviews-premium';
    if (is_dir($dir)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
    wp_clean_plugins_cache();
}

test('a call with no token, or one the site never made, is refused', function () {
    wp_set_current_user(0);
    $asked = licenseServer([]);

    $response = installPremium('not-a-token');

    expect($response->get_status())->toBe(403)
        ->and($response->get_data()['code'])->toBe('glsr_token')
        ->and($asked)->toHaveCount(0);
});

test('a token is good for one call', function () {
    wp_set_current_user(0);
    add_filter('file_mod_allowed', '__return_false'); // stops the first call right after the claim
    $token = premiumToken();

    $first = installPremium($token);
    $second = installPremium($token);

    expect($first->get_data()['code'])->toBe('glsr_cannot_install')
        ->and($second->get_status())->toBe(403)
        ->and($second->get_data()['code'])->toBe('glsr_token')
        ->and(get_transient(ConnectPremium::tokenKey($token)))->toBeFalse();
});

test('a package the server withholds is reported with the server\'s reason', function () {
    wp_set_current_user(0);
    premiumPackageServer('', ['msg' => 'No license key has been provided.']);

    $response = installPremium();

    expect($response->get_status())->toBe(500)
        ->and($response->get_data()['code'])->toBe('glsr_package')
        ->and($response->get_data()['message'])->toBe('No license key has been provided.')
        ->and($response->get_data()['link']['url'])->toContain('license-keys');
});

test('a package on another host than the licence server is not downloaded', function () {
    wp_set_current_user(0);
    premiumPackageServer('https://example.org/package_download/premium.zip');
    $downloads = 0;
    premiumPackageDownload(function () use (&$downloads) {
        ++$downloads;
        return ['response' => ['code' => 200, 'message' => 'OK']];
    });

    $response = installPremium();

    expect($response->get_data()['code'])->toBe('glsr_package')
        ->and($downloads)->toBe(0);
});

test('a download the licence server refuses with 401 is reported as a licence problem', function () {
    // The upgrader would rewrap this as download_failed with the reason phrase alone.
    wp_set_current_user(0);
    premiumPackageServer();
    premiumPackageDownload(fn () => ['response' => ['code' => 401, 'message' => 'Unauthorized']]);

    $response = installPremium();

    expect($response->get_data()['code'])->toBe('glsr_download')
        ->and($response->get_data()['message'])->toContain('no longer valid')
        ->and($response->get_data()['link']['url'])->toContain('license-keys');
});

test('any other failed download carries WordPress\'s own message', function () {
    wp_set_current_user(0);
    premiumPackageServer();
    premiumPackageDownload(fn () => new WP_Error('http_request_failed', 'cURL error 28: Operation timed out'));

    $response = installPremium();

    expect($response->get_data()['code'])->toBe('glsr_download')
        ->and($response->get_data()['message'])->toContain('cURL error 28')
        ->and($response->get_data()['link']['url'])->toContain('/account/');
});

test('the package is installed and premium activated for this site, as the token\'s user', function () {
    $userId = get_current_user_id();
    $token = premiumToken(); // made by the logged-in admin, as premium/connect makes it
    wp_set_current_user(0);
    $asked = premiumPackageServer();
    $package = premiumStubPackage();
    try {
        $response = installPremium($token);

        expect($response->get_status())->toBe(200)
            ->and($response->get_data()['installed'])->toBeTrue()
            ->and(get_transient(GeminiLabs\SiteReviews\Commands\InstallPremium::INSTALLED_KEY))->toBeInt() // premium's landing page reads it once
            ->and(array_values(array_filter($asked->getArrayCopy())))->toBe(['get_version']) // the update checks after the install ask api.wordpress.org, with no edd_action
            ->and(is_plugin_active(GeminiLabs\SiteReviews\Commands\InstallPremium::PLUGIN_FILE))->toBeTrue()
            ->and(get_current_user_id())->toBe($userId);
    } finally {
        removePremiumStub();
        if (file_exists($package)) {
            unlink($package); // the upgrader deletes a downloaded package after unpacking it
        }
    }
});

test('the language pack upgrade that admin-ajax hooks after an install is unhooked', function () {
    // admin-filters.php hooks it in an admin-ajax request; it would print into the JSON body.
    add_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20, 2);
    wp_set_current_user(0);
    premiumPackageServer();
    $package = premiumStubPackage();
    try {
        installPremium();

        expect(has_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade']))->toBeFalse();
    } finally {
        removePremiumStub();
        if (file_exists($package)) {
            unlink($package);
        }
    }
});

test('premium already on disk is activated without a download', function () {
    wp_set_current_user(0);
    $asked = premiumPackageServer();
    $dir = WP_PLUGIN_DIR.'/site-reviews-premium';
    try {
        mkdir($dir);
        file_put_contents("{$dir}/site-reviews-premium.php", "<?php\n/*\nPlugin Name: Site Reviews Premium (stub)\nVersion: 0.0.1\n*/\n");
        wp_clean_plugins_cache();

        $response = installPremium();

        expect($response->get_status())->toBe(200)
            ->and($asked)->toHaveCount(0)
            ->and(is_plugin_active(GeminiLabs\SiteReviews\Commands\InstallPremium::PLUGIN_FILE))->toBeTrue();
    } finally {
        removePremiumStub();
    }
});

test('an install whose activation fails says so and points at the Plugins screen', function () {
    wp_set_current_user(0);
    premiumPackageServer();
    $package = premiumStubPackage('not-the-main-file.php');
    try {
        $response = installPremium();

        expect($response->get_status())->toBe(500)
            ->and($response->get_data()['code'])->toBe('glsr_activation')
            ->and($response->get_data()['message'])->toContain('could not be activated')
            ->and($response->get_data()['link']['url'])->toEndWith('plugins.php')
            ->and(is_dir(WP_PLUGIN_DIR.'/site-reviews-premium'))->toBeTrue(); // installed, as the message says
    } finally {
        removePremiumStub();
        if (file_exists($package)) {
            unlink($package); // the upgrader deletes a downloaded package after unpacking it
        }
    }
});

test('the same call arrives over admin-ajax when the REST API refused it', function () {
    // The store's retry: core dispatches the carried REST request to the route in-process.
    wp_set_current_user(0);
    premiumPackageServer();
    $package = premiumStubPackage();
    try {
        $response = glsr(GeminiLabs\SiteReviews\Controllers\RestController::class)->ajaxResponse([
            'action' => 'glsr_rest_request',
            '_rest_method' => 'POST',
            '_rest_path' => 'premium/install',
            'token' => premiumToken(),
        ]);

        expect($response->get_status())->toBe(200)
            ->and($response->get_data()['installed'])->toBeTrue()
            ->and(is_plugin_active(GeminiLabs\SiteReviews\Commands\InstallPremium::PLUGIN_FILE))->toBeTrue();
    } finally {
        removePremiumStub();
        if (file_exists($package)) {
            unlink($package); // the upgrader deletes a downloaded package after unpacking it
        }
    }
});
