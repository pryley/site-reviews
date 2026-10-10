<?php

use GeminiLabs\SiteReviews\Controllers\LicensingController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\License;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Notices\LicenseExpiredNotice;
use GeminiLabs\SiteReviews\Notices\LicenseMissingNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Licences: the premium addons' keys, and the two banners that nag about them.
 *
 * None of this exists on a free site — the first thing to get right. Everything hangs off
 * glsr()->retrieve('licensed'), the addons that declared `const LICENSED = true`. Empty on a free
 * site, so License::status() reports `licensed => false` and both banners return immediately:
 * nagging a free user about a licence they never bought would be worse than wrong.
 *
 * With licensed addons, three states, each its own outcome:
 *
 *   missing   installed, no key entered.               → the "missing" banner
 *   expired   a key entered, licence run out.          → the "expired" banner
 *   invalid   key wrong, revoked, or another site's.   → an error on save
 *
 * Both banners are type `banner`, and AbstractNotice::canRender() lets only ONE banner render per
 * page — so an addon both missing a key and holding an expired one does not stack two.
 *
 * Everything the licence server says is a lie until proven: responses are restricted through
 * CheckLicenseDefaults before anything reads them, and the HTTP is intercepted so no test reaches
 * the real one.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
    set_current_screen(glsr()->post_type);
});

afterEach(function () {
    set_current_screen('front');
});

/**
 * The premium addon these tests licence. A function rather than a const, because a const declared
 * in a Pest test file is a GLOBAL constant and would collide with the next file that wanted one.
 */
function addonId(): string
{
    return 'site-reviews-images';
}

/**
 * A premium addon that has registered itself as one that needs a licence — which is what
 * `const LICENSED = true` on its Application class does (Application::append('licensed', …)).
 */
function licensedAddon(string $license = '', ?string $addonId = null): void
{
    $addonId ??= addonId();
    glsr()->append('licensed', ['name' => $addonId], $addonId);
    glsr(OptionManager::class)->set("settings.licenses.{$addonId}", $license);
}

// licenseServer() moved to Support/helpers.php: UpdateControllerTest replays the
// captured update-server fixtures through it too.

/**
 * What a notice printed.
 */
function renderedLicenseNotice(string $class): string
{
    ob_start();
    (new $class())->render();

    return (string) ob_get_clean();
}

/*
 * The free site, which is most of them.
 */

test('a site with no premium addons is never nagged about a licence', function () {
    // No licensed addons registered at all — which is the state of every free install. Neither
    // banner may render, and neither may make an HTTP request to find that out.
    $asked = licenseServer([]);

    expect(renderedLicenseNotice(LicenseMissingNotice::class))->toBe('')
        ->and(renderedLicenseNotice(LicenseExpiredNotice::class))->toBe('');
    expect($asked)->toHaveCount(0); // and the licence server was never asked
});

/*
 * The banners.
 */

test('an installed addon with no licence key is told so', function () {
    licensedAddon(''); // installed, never activated
    licenseServer([]);

    expect(renderedLicenseNotice(LicenseMissingNotice::class))
        ->toContain('glsr-notice-banner')
        ->toContain(LicenseMissingNotice::class);
});

test('an addon with no key is not ALSO told its licence expired', function () {
    // There is no licence to expire. The two banners read almost the same at a glance, and the
    // wrong one sends the person to renew something they never bought.
    licensedAddon('');
    licenseServer([]);

    expect(renderedLicenseNotice(LicenseExpiredNotice::class))->toBe('');
});

test('an addon whose licence has run out is told to renew it', function () {
    licensedAddon('a-licence-key');
    $asked = licenseServer(['check_license' => [
        'success' => true,
        'license' => 'expired',
        'expires' => '2020-01-01 23:59:59',
    ]]);

    expect(renderedLicenseNotice(LicenseExpiredNotice::class))
        ->toContain('glsr-notice-banner')
        ->toContain(LicenseExpiredNotice::class);
    // The key was really read back and the server was really asked — every other test in this
    // file would be satisfied by an empty licence key, so this is the one that proves the path.
    expect($asked->getArrayCopy())->toBe(['check_license']);
});

test('an addon with a licence in good standing is not nagged at all', function () {
    // The state every paying customer is in, on every admin page load. Neither banner.
    licensedAddon('a-licence-key');
    licenseServer(['check_license' => ['success' => true, 'license' => 'valid']]);

    expect(renderedLicenseNotice(LicenseMissingNotice::class))->toBe('')
        ->and(renderedLicenseNotice(LicenseExpiredNotice::class))->toBe('');
});

test('only one banner is shown at a time, however many are true', function () {
    // A real two-addon site: one installed with no key at all, one whose key has expired. BOTH
    // conditions hold, so both banners would render — and two full-width banners stacked above the
    // reviews table push the table off the screen.
    //
    // AbstractNotice::canRender() records that a banner has rendered and refuses every banner
    // after it, for the rest of the page load. Whichever runs first wins; the second is silent.
    licensedAddon('', 'site-reviews-images');            // missing
    licensedAddon('an-expired-key', 'site-reviews-woocommerce'); // expired
    licenseServer(['check_license' => [
        'success' => true,
        'license' => 'expired',
        'expires' => '2020-01-01 23:59:59',
    ]]);

    expect(renderedLicenseNotice(LicenseMissingNotice::class))->not->toBe('');
    expect(renderedLicenseNotice(LicenseExpiredNotice::class))->toBe(''); // true, and still not shown
});

/*
 * Saving a licence key on the settings page.
 */

test('a licence the server calls valid is kept', function () {
    $asked = licenseServer(['check_license' => ['success' => true, 'license' => 'valid']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-good-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('a-good-key');
    expect($asked->getArrayCopy())->toBe(['check_license']); // and it did not try to activate it
});

test('a licence the server does not recognise is thrown away, not saved', function () {
    // The whole point of checking on save. A key that is wrong, revoked, or belongs to somebody
    // else must not sit in the settings looking like it works — the person would never find out
    // why they were not getting updates.
    licenseServer(['check_license' => ['success' => false]]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-bad-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
    expect(glsr(Notice::class)->get())->toContain('invalid or has been revoked');
});

test('a licence that has been disabled is thrown away too', function () {
    // `success` is true — the server answered perfectly well. It is the ANSWER that is no.
    licenseServer(['check_license' => ['success' => true, 'license' => 'disabled']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-disabled-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
});

test('a saved licence is kept when the licence server gives no answer', function () {
    // An outage is not a verdict. Throwing the key away would lose it for good, and the
    // person would have to find it again before updates came back.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    licensedAddon('a-saved-key');
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-saved-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('a-saved-key')
        ->and(glsr(Notice::class)->get())->toContain('could not be reached')
        ->and(glsr(Notice::class)->get())->not->toContain('invalid or has been revoked');
});

test('a new licence is not saved when the licence server gives no answer', function () {
    // A key is only saved once the server has said it is valid.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-new-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('')
        ->and(glsr(Notice::class)->get())->toContain('could not be reached');
});

test('a changed licence keeps the saved one when the licence server gives no answer', function () {
    // The new key is not saved, and not saving it must not delete the key that was.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    licensedAddon('a-saved-key');
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-new-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('a-saved-key')
        ->and(glsr(Notice::class)->get())->toContain('could not be reached');
});

test('a licence that could not be checked is not reported as invalid', function () {
    // The status feeds the licence banners on every admin page. No answer says nothing
    // about the licence either way.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    licensedAddon('a-licence-key');
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    expect(glsr(License::class)->status()['invalid'])->toBeFalse();
});

test('an EXPIRED licence is kept, and the person is told to renew it', function () {
    // Deliberately not thrown away, and this is the one that would be easy to get wrong. An
    // expired licence is a real licence — it stops updates, it does not stop the addon working —
    // and deleting the key would mean the person has to dig it out of an email to renew.
    licenseServer(['check_license' => [
        'success' => true,
        'license' => 'expired',
        'expires' => '2020-01-01 23:59:59',
    ]]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'an-expired-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('an-expired-key'); // still there
    expect(glsr(Notice::class)->get())->toContain('has expired')
        ->toContain('renew');
});

test('a licence that is simply not activated here yet is activated, and kept', function () {
    // The ordinary path for somebody pasting their key in for the first time: the server says
    // "valid key, not activated on this site, and you have activations left", so the plugin
    // activates it for them rather than making them go and do it on their account page.
    $asked = licenseServer([
        'check_license' => ['success' => true, 'license' => 'inactive', 'activations_left' => 2],
        'activate_license' => ['success' => true, 'license' => 'valid'],
    ]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-new-key']],
    ]);

    expect($asked->getArrayCopy())->toBe(['check_license', 'activate_license']);
    expect($options['settings']['licenses'][addonId()])->toBe('a-new-key');
    expect(glsr(Notice::class)->get())->toContain('has been activated');
});

test('a licence with no activations left is not silently kept', function () {
    // Every seat is used on other sites. The plugin cannot fix that from here, so it says exactly
    // where to go and what to click — and does not save a key that would not work.
    licenseServer(['check_license' => [
        'success' => true,
        'license' => 'site_inactive',
        'license_limit' => 3,
        'activations_left' => 0,
    ]]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-used-up-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
    expect(glsr(Notice::class)->get())->toContain('Manage Sites');
});

test('an unlimited licence not yet activated here is activated like any other', function () {
    // An unlimited licence: license_limit 0 and activations_left 'unlimited', which the int cast turns into 0.
    $asked = licenseServer([
        'check_license' => [
            'success' => true,
            'license' => 'site_inactive',
            'license_limit' => 0,
            'activations_left' => 'unlimited',
        ],
        'activate_license' => ['success' => true, 'license' => 'valid'],
    ]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'an-unlimited-key']],
    ]);

    expect($asked->getArrayCopy())->toBe(['check_license', 'activate_license']);
    expect($options['settings']['licenses'][addonId()])->toBe('an-unlimited-key');
});

test('a licence for another product is thrown away, and the person is told which mistake it was', function () {
    // The server answers item_name_mismatch with success true (Requests/Check.php).
    licenseServer(['check_license' => ['success' => true, 'license' => 'item_name_mismatch']]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'another-addons-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
    expect(glsr(Notice::class)->get())->toContain('different product');
    expect(glsr(Notice::class)->get())->not->toContain('Manage Sites');
});

test('a saved key with no field on the form survives the save', function () {
    // The settings form posts a field for each registered addon only.
    $asked = licenseServer([]);
    $saved = ['settings' => ['licenses' => [
        'site-reviews-premium' => 'a-premium-key',
        'site-reviews-forms' => 'a-deactivated-addons-key',
        addonId() => 'an-old-key',
    ]]];

    $options = glsr(LicensingController::class)->sanitizeLicenses($saved, [
        'settings' => ['licenses' => [addonId() => '']], // cleared
    ]);

    expect($options['settings']['licenses'])->toBe([
        'site-reviews-premium' => 'a-premium-key',
        'site-reviews-forms' => 'a-deactivated-addons-key',
        addonId() => '',
    ]);
    expect($asked)->toHaveCount(0); // the unposted keys were not re-checked
});

test('an activation the server then refuses does not leave the key behind', function () {
    // The check said "inactive, activations left", the activation said no. Whatever the server is
    // doing, the key does not work here, so it is not saved.
    licenseServer([
        'check_license' => ['success' => true, 'license' => 'inactive', 'activations_left' => 1],
        'activate_license' => ['success' => true, 'license' => 'invalid'],
    ]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
});

test('an empty licence field is not sent to the server at all', function () {
    // Somebody clearing a key, or saving the settings page with none entered — which is every
    // save on a site with a premium addon it has not paid for yet. One HTTP request per empty
    // field, on every settings save, would be a slow settings page and a puzzled server.
    $asked = licenseServer([]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => '']],
    ]);

    expect($asked)->toHaveCount(0);
    expect($options['settings']['licenses'][addonId()])->toBe('');
});

test('a premium licence marks the whole status premium', function () {
    licensedAddon('a-valid-premium-key');
    licenseServer([
        'check_license' => ['license' => 'valid', 'is_premium_license' => true],
    ]);

    $status = glsr(License::class)->status();

    expect($status['licensed'])->toBeTrue()
        ->and($status['premium'])->toBeTrue()
        ->and($status['expired'])->toBeFalse();
});

test('the key the upgrade page saved counts as premium before premium is installed', function () {
    // No premium addon is registered, so the loop over licensed addons never sees this key.
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-premium', 'a-premium-key');
    $asked = licenseServer([
        'check_license' => ['success' => true, 'license' => 'valid', 'is_premium_license' => true],
    ]);

    $status = glsr(License::class)->status();

    expect($status['licensed'])->toBeTrue()
        ->and($status['missing'])->toBeFalse()
        ->and($status['premium'])->toBeTrue()
        ->and(glsr(License::class)->premiumKey())->toBe('a-premium-key')
        ->and($asked->getArrayCopy())->toBe(['check_license']); // status() ran twice; the second read the day's cache
});

test('a flagged key of an addon is the premium key too', function () {
    // An All Access key is flagged for every item it is checked against.
    licensedAddon('an-all-access-key');
    licenseServer([
        'check_license' => ['success' => true, 'license' => 'valid', 'is_premium_license' => true],
    ]);

    expect(glsr(License::class)->premiumKey())->toBe('an-all-access-key');
});

test('an expired flagged key is not offered for the install', function () {
    licensedAddon('an-expired-all-access-key');
    licenseServer([
        'check_license' => ['success' => true, 'license' => 'expired', 'expires' => '2020-01-01 23:59:59', 'is_premium_license' => true],
    ]);

    $status = glsr(License::class)->status();

    expect($status['premium'])->toBeTrue() // as before: the pitch is hidden
        ->and($status['expired'])->toBeTrue()
        ->and(glsr(License::class)->premiumKey())->toBe('');
});

test('an installed premium plugin is premium before any licence is entered', function () {
    // The merged premium plugin hides every "Upgrade to Premium" pitch the moment it is
    // installed — a paying customer should never be sold what they already bought. The licence
    // banners still apply though: a key is what gets them updates.
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Application.php');
    require_once glsr()->path('tests/pest/fixtures/site-reviews-premium/plugin/Hooks.php');
    $asked = licenseServer([]);

    expect(glsr(License::class)->isPremium())->toBeFalse();

    // Registration lands on the Application singleton's $addons property,
    // which no teardown resets — so it is backed up and restored by hand.
    $registry = new ReflectionProperty(get_class(glsr()), 'addons');
    $registry->setAccessible(true);
    $registered = $registry->getValue(glsr());
    try {
        glsr()->register(GeminiLabs\SiteReviews\Premium\Shell\Application::class);
        $status = glsr(License::class)->status();
    } finally {
        $registry->setValue(glsr(), $registered);
    }

    expect($status['premium'])->toBeTrue()
        ->and($status['missing'])->toBeTrue() // no key entered: the missing banner still nags
        ->and($asked)->toHaveCount(0); // and nobody asked the licence server to find that out
});
