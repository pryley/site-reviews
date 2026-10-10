<?php

use GeminiLabs\SiteReviews\Commands\InstallPremium;
use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Controllers\SettingsController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html\SettingForm;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\PremiumLicense;
use GeminiLabs\SiteReviews\Notices\LicensePremiumNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The License Key row at the top of Settings > General. Premium on disk is a stub
 * folder in the plugins directory; premium registered is the suite's fixture.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    licenseServer([]);
});

function premiumRow(): string
{
    return glsr(PremiumLicense::class)->render();
}

function savedValidPremiumKey(array $check = [], string $key = 'a-premium-key'): void
{
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', $key);
    licenseServer(['check_license' => array_replace([
        'success' => true,
        'license' => 'valid',
        'expires' => date('Y-m-d H:i:s', strtotime('+1 year')),
        'is_premium_license' => true,
    ], $check)]);
}

function withPremiumOnDisk(callable $callback): void
{
    $dir = WP_PLUGIN_DIR.'/site-reviews-premium';
    try {
        mkdir($dir);
        file_put_contents("{$dir}/site-reviews-premium.php", "<?php\n/*\nPlugin Name: Site Reviews Premium (stub)\nVersion: 0.0.1\n*/\n");
        $callback();
    } finally {
        unlink("{$dir}/site-reviews-premium.php");
        rmdir($dir);
    }
}

function withPremiumRegistered(callable $callback): void
{
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Hooks.php');
    $registry = new ReflectionProperty(get_class(glsr()), 'addons');
    $registry->setAccessible(true);
    $registered = $registry->getValue(glsr());
    try {
        glsr()->register(GeminiLabs\SiteReviews\Premium\Shell\Application::class);
        $callback();
    } finally {
        $registry->setValue(glsr(), $registered);
    }
}

/*
 * The four states.
 */

test('with no key the row offers Verify under the upgrade notice', function () {
    $row = premiumRow();

    expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INACTIVE)
        ->and($row)->toContain('data-state="1"')
        ->and($row)->toContain('notice-info')
        ->and($row)->toContain('You are using the free Site Reviews plugin')
        ->and($row)->toContain('class="glsr-button button button-primary" data-action="verify" data-loading="Verifying, please wait..."')
        ->and($row)->not->toContain('data-action="delete"')
        ->and($row)->toContain('Already purchased?')
        ->and($row)->toContain('name="site_reviews[settings][licenses][site-reviews-premium]"')
        ->and($row)->toContain('placeholder="Paste license key"')
        ->and($row)->not->toContain('readonly');
});

test('a saved key the server refuses stays in the field with Delete, the reason, and the warning above', function ($check, $type, $sentence) {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-saved-key');
    licenseServer(['check_license' => $check]);

    $row = premiumRow();

    expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INACTIVE)
        ->and($row)->toContain('value="a-saved-key"')
        ->and($row)->toContain('data-action="verify"')
        ->and($row)->toContain('data-action="delete"')
        ->and($row)->toContain('notice-warning inline"')
        ->and($row)->toContain('cannot be installed until a valid license key is verified')
        ->and($row)->not->toContain('You are using the free Site Reviews plugin')
        ->and($row)->toContain('notice-'.$type.' inline glsr-premium-license__message')
        ->and($row)->toContain($sentence)
        ->and($row)->not->toContain('Already purchased?');
})->with([
    'invalid' => [['success' => false, 'license' => 'invalid'], 'error', 'invalid license key for Site Reviews Premium'],
    'disabled' => [['success' => true, 'license' => 'disabled'], 'error', 'has been disabled. Please <a href="https://niftyplugins.com/account/support/" target="_blank">contact support</a> for more information.'],
    'expired, renewal link from the server' => [['success' => true, 'license' => 'expired', 'expires' => '2020-01-01 23:59:59', 'renewal_url' => 'https://niftyplugins.com/checkout/?edd_license_key=a-saved-key'], 'error', 'expired on January 1, 2020. Please <a href="https://niftyplugins.com/checkout/?edd_license_key=a-saved-key" target="_blank">renew it</a>.'],
    'expired, no renewal link' => [['success' => true, 'license' => 'expired', 'expires' => '2020-01-01 23:59:59'], 'error', 'license-keys'],
    'not active here, activations left' => [['success' => true, 'license' => 'site_inactive', 'license_limit' => 2, 'activations_left' => 1], 'warning', 'Click Verify License Key to activate it.'],
    'not active here, none left' => [['success' => true, 'license' => 'site_inactive', 'license_limit' => 1, 'activations_left' => 0], 'warning', 'reached its activation limit'],
    'another product' => [['success' => true, 'license' => 'item_name_mismatch'], 'error', 'invalid license key for Site Reviews Premium'],
]);

test('a valid key is masked and read-only, with Deactivate, Install and the expiry date', function () {
    savedValidPremiumKey(['expires' => '2030-06-15 23:59:59']);

    $row = premiumRow();

    expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_ACTIVE)
        ->and($row)->toContain('data-state="2"')
        ->and($row)->toContain('notice-info inline"')
        ->and($row)->toContain('Your license is active. Install Site Reviews Premium to unlock all features.')
        ->and($row)->not->toContain('You are using the free Site Reviews plugin')
        ->and($row)->toContain('readonly')
        ->and($row)->toContain('wp-hide-pw')
        ->and($row)->toContain('data-action="deactivate"')
        ->and($row)->toContain('data-action="install"')
        ->and($row)->toContain('Your license key expires on June 15, 2030.')
        ->and($row)->not->toContain('subscription');
});

test('the status line follows EDD for a lifetime key, one expiring soon, and a subscription', function () {
    // The check is cached for the day by key, so each case is its own key.
    savedValidPremiumKey(['expires' => 'lifetime'], 'a-lifetime-key');
    expect(premiumRow())->toContain('License key never expires.');

    savedValidPremiumKey(['expires' => date('Y-m-d H:i:s', strtotime('+10 days'))], 'a-key-expiring-soon');
    expect(premiumRow())->toContain('notice-warning inline glsr-premium-license__message')
        ->toContain('Your license key expires soon!');

    savedValidPremiumKey(['subscription' => 'active'], 'a-subscribed-key');
    expect(premiumRow())->toContain('Your license subscription is active and will automatically renew.');

    savedValidPremiumKey(['subscription' => 'cancelled'], 'a-cancelled-key');
    expect(premiumRow())->toContain('Your license subscription is cancelled and will not automatically renew.');
});

test('one drawing of the row asks the licence server once, and says nothing of upgrading when it did not answer', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    $asked = licenseServer(['check_license' => ['success' => true, 'license' => 'valid', 'expires' => '2030-06-15 23:59:59']]);

    premiumRow();

    expect($asked->getArrayCopy())->toBe(['check_license']);

    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    glsr(PremiumLicense::class)->flush();
    GeminiLabs\SiteReviews\Tests\interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);
    $row = premiumRow();

    expect($row)->toContain('data-state="1"')
        ->and($row)->toContain('could not be reached')
        ->and($row)->not->toContain('data-action="delete"')
        ->and($row)->not->toContain('notice-info')
        ->and($row)->not->toContain('You are using the free Site Reviews plugin');
});

test('a user who cannot install gets the manual zip sentence instead of Install', function () {
    add_filter('file_mod_allowed', '__return_false');
    savedValidPremiumKey();

    $row = premiumRow();

    expect($row)->toContain('data-state="2"')
        ->and($row)->not->toContain('data-action="install"')
        ->and($row)->toContain('Your license is active. Download Site Reviews Premium from <a')
        ->and($row)->toContain('data-action="deactivate"');
});

test('premium on disk gets the activation link, which is the Plugins screen\'s own', function () {
    savedValidPremiumKey();
    withPremiumOnDisk(function () {
        $row = premiumRow();

        expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_ON_DISK)
            ->and($row)->toContain('Activate Site Reviews Premium')
            ->and($row)->toContain('plugins.php?action=activate&amp;plugin=site-reviews-premium%2Fsite-reviews-premium.php')
            ->and($row)->not->toContain('trigger=notice') // core's NoticeController would intercept it and activate silently
            ->and($row)->toContain('_wpnonce=')
            ->and($row)->toContain('installed but not active. Activate it to unlock all features.')
            ->and($row)->not->toContain('data-action="install"');
    });
});

test('premium registered gets Features, and says so when it has no key', function () {
    withPremiumRegistered(function () {
        expect(premiumRow())->toContain('notice-warning inline"')
            ->and(premiumRow())->toContain('Site Reviews Premium is not receiving updates because it has no valid license key. Verify your license key to receive updates again.')
            ->and(premiumRow())->not->toContain('glsr-premium-license__message')
            ->and(premiumRow())->not->toContain('You are using the free Site Reviews plugin');

        add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
        glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'an-unchecked-key');
        GeminiLabs\SiteReviews\Tests\interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);
        expect(premiumRow())->toContain('could not be reached')
            ->and(premiumRow())->not->toContain('not receiving updates'); // nothing is known

        savedValidPremiumKey();
        $row = premiumRow();
        expect(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INSTALLED)
            ->and($row)->toContain('data-state="4"')
            ->and($row)->not->toContain('notice-info inline"')
            ->and($row)->not->toContain('notice-warning inline"')
            ->and($row)->toContain('page=glsr-premium')
            ->and($row)->toContain('Features')
            ->and($row)->not->toContain('data-action="install"')
            ->and($row)->toContain('data-action="deactivate"');
    });
});

test('a flagged addon key is the prefill until a key is saved under premium', function () {
    glsr()->append('licensed', ['name' => 'site-reviews-images'], 'site-reviews-images');
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'an-all-access-key');
    licenseServer(['check_license' => ['success' => true, 'license' => 'valid', 'is_premium_license' => true]]);

    $row = premiumRow();

    expect(glsr(PremiumLicense::class)->key())->toBe('an-all-access-key')
        ->and(glsr(PremiumLicense::class)->state())->toBe(PremiumLicense::STATE_INACTIVE) // not saved under premium yet
        ->and($row)->toContain('value="an-all-access-key"')
        ->and($row)->not->toContain('data-action="delete"')
        ->and($row)->not->toContain('Already purchased?')
        ->and($row)->toContain('Your license includes Site Reviews Premium. Verify your license key to install it.'); // the addon is licensed but not registered
});

test('the notices name the active addons premium replaces', function () {
    $registry = new ReflectionProperty(get_class(glsr()), 'addons');
    $registry->setAccessible(true);
    $registered = $registry->getValue(glsr());
    try {
        glsr()->register(GeminiLabs\SiteReviews\TestAddon\Application::class);

        expect(glsr(PremiumLicense::class)->addonNames())->toBe(['Test Addon'])
            ->and(premiumRow())->toContain('You are using Test Addon. Site Reviews Premium replaces it and adds more features');

        glsr(OptionManager::class)->set('settings.licenses.site-reviews-test-addon', 'an-all-access-key');
        licenseServer(['check_license' => ['success' => true, 'license' => 'valid', 'is_premium_license' => true]]);
        expect(premiumRow())->toContain('You are using Test Addon, and your license includes Site Reviews Premium, which replaces it. Verify your license key to install it.');

        glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-saved-key');
        licenseServer(['check_license' => ['success' => false, 'license' => 'invalid']]);
        expect(premiumRow())->toContain('You are using Test Addon, which it replaces.');

        savedValidPremiumKey([], 'another-valid-key');
        expect(premiumRow())->toContain('Install Site Reviews Premium to unlock all features. It replaces Test Addon with one plugin and imports its settings.');
    } finally {
        $registry->setValue(glsr(), $registered);
    }
});

/*
 * The tabs around it.
 */

test('the General tab opens with the row, and the Licenses tab appears only for standalone addons', function () {
    $form = (new SettingForm(['general' => 'General']))->build();
    $generalHeading = strpos($form, 'General Settings');

    expect($form)->toContain('id="glsr-premium-license"')
        ->and(strpos($form, 'id="glsr-premium-license"'))->toBeLessThan($generalHeading);

    ob_start();
    glsr(MenuController::class)->renderSettingsMenuCallback();
    $page = (string) ob_get_clean();
    expect($page)->not->toContain('data-id="licenses"');

    glsr()->append('licensed', ['name' => 'site-reviews-images'], 'site-reviews-images');
    ob_start();
    glsr(MenuController::class)->renderSettingsMenuCallback();
    $page = (string) ob_get_clean();
    expect($page)->toContain('data-id="licenses"');
});

test('premium\'s own key has no row on the Licenses tab', function () {
    withPremiumRegistered(function () {
        glsr()->register(GeminiLabs\SiteReviews\TestAddon\Application::class); // declares LICENSED, so it gets a row
        $settings = new ReflectionProperty(get_class(glsr()), 'settings');
        $settings->setAccessible(true);
        $settings->setValue(glsr(), []); // rebuilt with the two licence rows
        $form = (new SettingForm(['licenses' => 'Licenses']))->build();
        $settings->setValue(glsr(), []);

        expect($form)->toContain('[licenses][site-reviews-test-addon]')
            ->and($form)->not->toContain('[licenses][site-reviews-premium]');
    });
});

test('a key posted from the row is saved by the settings form while premium is not registered', function () {
    $asked = licenseServer(['check_license' => ['success' => true, 'license' => 'valid', 'is_premium_license' => true]]);
    $input = ['settings' => ['licenses' => ['site-reviews-premium' => 'a-premium-key']]];

    $saved = glsr(SettingsController::class)->sanitizeSettingsCallback($input);

    expect($asked->getArrayCopy())->toContain('check_license')
        ->and($saved['settings']['licenses']['site-reviews-premium'])->toBe('a-premium-key');
});

/*
 * The notice, the submenu label, the landing message and the rollback warning.
 */

test('the submenu reads "Install Premium" for a flagged key with no premium on disk, and the pitch otherwise', function () {
    $title = protectedMethod(MenuController::class, 'premiumMenuTitle');
    $controller = glsr(MenuController::class);

    expect($title->invoke($controller))->toBe('Upgrade to Premium');

    savedValidPremiumKey();
    expect($title->invoke($controller))->toBe('Install Premium');

    withPremiumOnDisk(fn () => expect($title->invoke($controller))->toBe('Upgrade to Premium'));
});

test('a flagged key with no premium on disk gets the install notice, linking to the row', function () {
    set_current_screen('edit-'.glsr()->post_type);
    glsr()->store('notices', []);
    savedValidPremiumKey();
    try {
        ob_start();
        (new LicensePremiumNotice())->render();
        $notice = (string) ob_get_clean();

        expect($notice)->toContain('glsr-notice-banner')
            ->toContain('Your license includes Site Reviews Premium')
            ->toContain('page=glsr-settings')
            ->toContain('data-glsr-premium-install');

        // an editor cannot act on it
        wp_set_current_user(createUser(['role' => 'editor']));
        $notice = new LicensePremiumNotice();
        expect(has_action('admin_notices', [$notice, 'render']))->toBeFalse();
    } finally {
        set_current_screen('front');
    }
});

test('the install notice is silent with no flagged key, on the Settings page, and once premium is on disk', function () {
    set_current_screen('edit-'.glsr()->post_type);
    glsr()->store('notices', []);
    try {
        ob_start();
        (new LicensePremiumNotice())->render();
        expect((string) ob_get_clean())->toBe('');

        savedValidPremiumKey();
        withPremiumOnDisk(function () {
            ob_start();
            (new LicensePremiumNotice())->render();
            expect((string) ob_get_clean())->toBe('');
        });

        set_current_screen(glsr()->post_type.'_page_glsr-settings');
        $notice = new LicensePremiumNotice();
        expect(has_action('admin_notices', [$notice, 'render']))->toBeFalse();
    } finally {
        set_current_screen('front');
    }
});

test('the Settings page says once that premium is installed, from the mark the install left', function () {
    glsr(Notice::class)->clear();
    set_transient(InstallPremium::INSTALLED_KEY, time(), MINUTE_IN_SECONDS);
    $notice = protectedMethod(MenuController::class, 'noticePremiumInstalled');

    $notice->invoke(glsr(MenuController::class)); // premium not registered: the mark is consumed, nothing said
    expect(glsr(Notice::class)->get())->not->toContain('installed and active')
        ->and(get_transient(InstallPremium::INSTALLED_KEY))->toBeFalse();

    set_transient(InstallPremium::INSTALLED_KEY, time(), MINUTE_IN_SECONDS);
    withPremiumRegistered(function () use ($notice) {
        $notice->invoke(glsr(MenuController::class));
        expect(glsr(Notice::class)->get())->toContain('Site Reviews Premium is installed and active.')
            ->and(get_transient(InstallPremium::INSTALLED_KEY))->toBeFalse();
        glsr(Notice::class)->clear();
        $notice->invoke(glsr(MenuController::class));
        expect(glsr(Notice::class)->get())->not->toContain('installed and active');
    });
});

test('the rollback card warns which registered addons a version would stop', function () {
    $registry = new ReflectionProperty(get_class(glsr()), 'addons');
    $registry->setAccessible(true);
    $registered = $registry->getValue(glsr());
    try {
        glsr()->register(GeminiLabs\SiteReviews\TestAddon\Application::class); // GLSR requires at least: 8.0.0
        $warnings = protectedMethod(MenuController::class, 'rollbackWarnings')
            ->invoke(glsr(MenuController::class), ['8.4.0', '8.0.0', '7.9.0']);

        expect(array_keys($warnings))->toBe(['7.9.0'])
            ->and($warnings['7.9.0'])->toContain('After the rollback to 7.9.0, Test Addon will stop working')
            ->and($warnings['7.9.0'])->not->toContain('deactivated stay inactive');

        $page = glsr()->build('pages/tools/general/rollback-plugin', [
            'rollback_script' => '',
            'rollback_versions' => ['8.4.0', '7.9.0'],
            'rollback_warnings' => $warnings,
        ]);
        expect($page)->toContain('value="7.9.0" data-warning="After the rollback to 7.9.0, Test Addon will stop working')
            ->and($page)->toContain('value="8.4.0" data-warning=""')
            ->and($page)->toContain('id="rollback-warning"');
    } finally {
        $registry->setValue(glsr(), $registered);
    }
});
