<?php

use GeminiLabs\SiteReviews\Addons\UpdateNotice;
use GeminiLabs\SiteReviews\Addons\Updater;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The sentence shown when an addon update has no download package.
 *
 * The update server withholds `package` when the licence does not allow the update and
 * says why in `license_status`. Each status gets its own sentence and its own place to go:
 * the settings page for a key that needs entering, checking or activating; the renewal
 * link the server sent (or the account's License Keys page) for an expired one; support
 * for a disabled one. A status the plugin does not know, or no status at all — which is
 * what a server that predates the field sends — reads as the generic sentence, linked to
 * the plugin's page on the store, exactly as before.
 */

beforeEach(function () {
    resetPluginState();
});

test('each licence status has its own sentence and its own remedy', function (string $status, string $text, string $remedy) {
    $remedies = [
        'license-keys' => glsr_premium_url('license-keys'),
        'settings' => glsr_admin_url('settings', 'licenses'),
        'support' => glsr_premium_url('support'),
    ];
    $notice = new UpdateNotice($status);

    expect($notice->text())->toBe($text)
        ->and($notice->url())->toBe($remedies[$remedy]);
})->with([
    'missing' => ['missing', 'Enter your license key to update this plugin.', 'settings'],
    'expired' => ['expired', 'Your license has expired. Renew it to update this plugin.', 'license-keys'],
    'site inactive' => ['site_inactive', 'Your license is not activated for this site. Activate it to update this plugin.', 'settings'],
    'inactive' => ['inactive', 'Your license is not activated for this site. Activate it to update this plugin.', 'settings'],
    'disabled' => ['disabled', 'Your license has been disabled. Please contact support to update this plugin.', 'support'],
    'invalid' => ['invalid', 'Your license key was not recognized. Check it to update this plugin.', 'settings'],
    'wrong item id' => ['invalid_item_id', 'Your license key is for a different plugin. Check it to update this plugin.', 'settings'],
    'wrong item name' => ['item_name_mismatch', 'Your license key is for a different plugin. Check it to update this plugin.', 'settings'],
]);

test('an expired licence renews at the link the server sent', function () {
    $renewal = 'https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED';

    expect((new UpdateNotice('expired', $renewal))->url())->toBe($renewal)
        // and without one, at the account's License Keys page
        ->and((new UpdateNotice('expired'))->url())->toBe('https://niftyplugins.com/account/license-keys/');
});

test('no status, or one the plugin does not know, is the generic sentence linked to the store', function () {
    $generic = 'A valid license key is required to update this plugin.';

    expect((new UpdateNotice())->text())->toBe($generic)
        ->and((new UpdateNotice())->url())->toBe(Updater::DEFAULT_API_URL)
        ->and((new UpdateNotice('something_new'))->text())->toBe($generic)
        // the plugin's own page on the store, when the row knows it
        ->and((new UpdateNotice('', '', 'https://niftyplugins.com/plugins/site-reviews-alerts/'))->url())
            ->toBe('https://niftyplugins.com/plugins/site-reviews-alerts/');
});

test('the row gets the sentence with the remedy as its link', function () {
    $html = (new UpdateNotice('expired', 'https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED'))->html();

    expect($html)->toBe('Your license has expired. <a href="https://niftyplugins.com/checkout/?edd_license_key=SCRUBBED">Renew it</a> to update this plugin.');

    // the generic sentence renders as it always has
    $html = (new UpdateNotice('', '', 'https://niftyplugins.com/plugins/site-reviews-alerts/'))->html();

    expect($html)->toBe('A valid <a href="https://niftyplugins.com/plugins/site-reviews-alerts/">license key</a> is required to update this plugin.');
});
