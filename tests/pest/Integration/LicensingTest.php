<?php

use GeminiLabs\SiteReviews\Controllers\LicensingController;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Notices\LicenseExpiredNotice;
use GeminiLabs\SiteReviews\Notices\LicenseMissingNotice;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Licences: the premium addons' keys, and the two banners that nag about them.
 *
 * None of this exists on a free site, and that is the first thing to get right. Everything here
 * hangs off glsr()->retrieve('licensed') — the addons that declared `const LICENSED = true` when
 * they registered. On a site with no premium addons that list is empty, License::status() reports
 * `licensed => false`, and both banners return immediately. A plugin that nagged a free user about
 * a licence they were never asked to buy would be doing something worse than being wrong.
 *
 * When there ARE licensed addons, three things can be true of each, and each has its own outcome:
 *
 *   missing   the addon is installed and no key has been entered.        → the "missing" banner
 *   expired   a key was entered, and the licence has since run out.      → the "expired" banner
 *   invalid   the key is wrong, revoked, or for somebody else's site.    → an error on save
 *
 * Both banners are of type `banner`, and AbstractNotice::canRender() lets only ONE banner render
 * per page — so an addon that is both missing a key and holding an expired one does not stack two
 * of them on top of each other.
 *
 * Everything the licence server says is a lie until proven otherwise: the responses are restricted
 * through CheckLicenseDefaults before anything reads them, and the HTTP is intercepted here so no
 * test ever reaches the real one.
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

/**
 * The licence server, answering each edd_action differently — which a single canned response
 * cannot do, and activation needs: the check says "inactive", and the activation that follows it
 * says "valid".
 *
 * @param array<string, array> $responses keyed by edd_action
 *
 * @return \ArrayObject<int, string> every action that was asked for, in order
 */
function licenseServer(array $responses): ArrayObject
{
    $asked = new ArrayObject();
    add_filter('pre_http_request', function ($pre, $args, $url) use ($responses, $asked) {
        $action = (string) ($args['body']['edd_action'] ?? '');
        $asked->append($action);

        return [
            'body' => (string) wp_json_encode($responses[$action] ?? []),
            'cookies' => [],
            'filename' => null,
            'headers' => [],
            'http_response' => null,
            'response' => ['code' => 200, 'message' => 'OK'],
        ];
    }, 10, 3);

    return $asked;
}

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
        'activations_left' => 0,
    ]]);

    $options = glsr(LicensingController::class)->sanitizeLicenses([], [
        'settings' => ['licenses' => [addonId() => 'a-used-up-key']],
    ]);

    expect($options['settings']['licenses'][addonId()])->toBe('');
    expect(glsr(Notice::class)->get())->toContain('Manage Sites');
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
