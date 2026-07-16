<?php

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Database\OptionManager;

use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The licence, and the update it entitles someone to.
 *
 * The code that runs when a person pastes in the key they just paid for, and that decides whether
 * they are offered the addon update afterwards. It speaks an EDD software-licensing dialect: one
 * POST per action, `edd_action` says which. The tests check:
 *
 *   what goes UP    the licence key, the addon it is for, and the URL of the site claiming an
 *                   activation. Wrong site URL and the person burns an activation slot on a site
 *                   they do not own.
 *   what comes DOWN whatever the server sent, RESTRICTED through a Defaults class before anything
 *                   reads it — the version payload carries `package`, the URL WordPress is handed to
 *                   download and install a zip from.
 *
 * Every licence call flushes the cached get_version first, so someone who just activated is offered
 * the download, not yesterday's "no".
 */

beforeEach(function () {
    resetPluginState();
});

function updater(array $args = []): Updater
{
    return new Updater('site-reviews-images', wp_parse_args($args, [
        'url' => 'https://updates.example.org',
    ]));
}

/**
 * The body of the request the updater sent.
 */
function sentBody(ArrayObject $requests, int $index = 0): array
{
    return (array) ($requests[$index]['args']['body'] ?? []);
}

/*
 * Where it asks, and with what.
 */

test('the licence comes from the settings when it is not passed in', function () {
    // Which is how every real caller uses it: the key lives in settings.licenses.{addon}.
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-real-licence-key');

    expect(updater()->license)->toBe('a-real-licence-key');
    expect(updater(['license' => 'an-explicit-key'])->license)->toBe('an-explicit-key');
});

test('an addon that is not installed is asked about at the default api', function () {
    // updateUri() reads the Update URI header of the addon's plugin file. An addon that is
    // not installed has no header to read, and niftyplugins.com is where the rest live.
    expect((new Updater('site-reviews-not-installed'))->apiUrl)->toBe(Updater::DEFAULT_API_URL);
});

test('activating a licence says which addon, which key, and which site', function () {
    // `url` is the site claiming the activation. If it were wrong, somebody would spend one
    // of the activations they paid for on a site that is not theirs.
    $requests = interceptHttp(['body' => (string) wp_json_encode(['success' => true, 'license' => 'valid'])]);

    updater(['license' => 'a-real-licence-key'])->activateLicense();

    expect(sentBody($requests))->toMatchArray([
        'edd_action' => 'activate_license',
        'item_name' => 'site-reviews-images',
        'license' => 'a-real-licence-key',
        'slug' => 'site-reviews-images',
        // Url::home() is trailingslashit(network_home_url()) — the NETWORK home, so on
        // multisite every site in the network claims the activation as the network, and one
        // licence covers the lot. Trailing slash included: the licence server keys on this
        // string, and 'https://example.org' and 'https://example.org/' are two sites to it.
        'url' => trailingslashit(network_home_url()),
    ]);
    expect($requests[0]['url'])->toBe('https://updates.example.org');
});

test('checking and deactivating are the same request with a different verb', function () {
    $requests = interceptHttp(['body' => (string) wp_json_encode(['success' => true])]);

    updater()->checkLicense();
    updater()->deactivateLicense();

    expect(sentBody($requests, 0)['edd_action'])->toBe('check_license')
        ->and(sentBody($requests, 1)['edd_action'])->toBe('deactivate_license');
});

test('every version call is the same get_version request', function () {
    // Three shapes of the same answer, for three consumers: the update transient, the
    // plugin-information modal, and the addons page.
    $requests = interceptHttp(['body' => (string) wp_json_encode(['new_version' => '2.0.0'])]);

    updater()->version();
    updater()->versionDetails();
    updater()->versionUpdate();

    foreach ([0, 1, 2] as $i) {
        expect(sentBody($requests, $i)['edd_action'])->toBe('get_version');
    }
});

/*
 * What comes back, and what is allowed through.
 */

test('a licence response is restricted to the keys the plugin knows about', function () {
    // The server is not trusted to send only what was asked for. Anything else is dropped
    // before the plugin reads it, and the keys that were not sent come back as defaults —
    // so a caller never has to ask whether a key is there.
    $requests = interceptHttp(['body' => (string) wp_json_encode([
        'success' => true,
        'license' => 'valid',
        'expires' => '2027-01-01 23:59:59',
        'activations_left' => 3,
        'malicious_key' => '<script>alert(1)</script>',
    ])]);

    $result = updater(['license' => 'a-real-licence-key'])->activateLicense();

    expect($result['success'])->toBeTrue()
        ->and($result['license'])->toBe('valid')
        ->and($result['expires'])->toBe('2027-01-01 23:59:59')
        ->and($result['activations_left'])->toEqual(3)
        ->and($result)->not->toHaveKey('malicious_key')
        ->and($result)->toHaveKey('site_count') // and the ones it did not send are still there
        ->and($result['error'])->toBe('');
    expect($requests)->toHaveCount(1);
});

test('an unlicensed check comes back invalid rather than empty', function () {
    // CheckLicenseDefaults defaults `license` to 'invalid', not to ''. Everything that reads
    // it is asking "is this licensed", and an empty string that is treated as "not yet
    // checked" is how an unlicensed site ends up being offered a premium download.
    interceptHttp(['body' => (string) wp_json_encode([])]);

    $result = updater()->checkLicense();

    expect($result['license'])->toBe('invalid')
        ->and($result['success'])->toBeFalse()
        ->and($result['is_premium_license'])->toBeFalse();
});

test('a server that is down does not become an update', function () {
    // The single most important negative in this file. A failed request must not produce a
    // version payload with a `package` in it, and it must not produce an empty `new_version`
    // that something downstream reads as "there is an update".
    //
    // max_retries is turned down to one attempt, because a 5xx is now retried in earnest and
    // the backoff really does sleep. What is under test is the payload, not the retrying.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    interceptHttp(['response' => ['code' => 500, 'message' => 'Internal Server Error']]);

    $version = updater()->versionUpdate();

    expect($version['version'])->toBe('')
        ->and($version['package'])->toBe('')
        ->and($version['slug'])->toBe('');
});

test('the details for the plugin-information modal are shaped for wordpress', function () {
    interceptHttp(['body' => (string) wp_json_encode([
        'name' => 'Site Reviews: Images',
        'version' => '2.0.0',
        'requires_php' => '8.1',
        'download_link' => 'https://updates.example.org/download/images.zip',
        'sections' => ['description' => 'Adds images to reviews.'],
        'new_version' => '2.0.0', // get_version sends it; the modal has no use for it
    ])]);

    $details = updater()->versionDetails();

    expect($details['name'])->toBe('Site Reviews: Images')
        ->and($details['version'])->toBe('2.0.0')
        ->and($details['requires_php'])->toBe('8.1')
        ->and($details['sections'])->toBe(['description' => 'Adds images to reviews.'])
        ->and($details)->not->toHaveKey('new_version')
        ->and($details['autoupdate'])->toBeTrue();
});

/*
 * The flush. This is the fix that mattered: it is what stops somebody who has just paid
 * being told there is nothing to download.
 */

test('activating a licence throws away the cached answer from before they had one', function () {
    // `force` is true by default on the Updater, which is why the licence calls always go
    // out. The VERSION check is the one that is allowed to be cached — for a day — and it is
    // therefore the one that goes stale the moment somebody activates a licence.
    $requests = interceptHttp(['body' => (string) wp_json_encode(['new_version' => '2.0.0'])]);
    $unlicensed = updater(['force' => false, 'license' => '']);

    $unlicensed->version();
    $unlicensed->version();
    expect($requests)->toHaveCount(1); // the second came out of the cache, as it should

    // They paste in the key they have paid for. activateLicense() flushes get_version first,
    // and the answer it is flushing was cached under a body containing the OLD (empty)
    // licence, so its key cannot be worked out — it is found through Api's index.
    updater(['license' => 'a-real-licence-key'])->activateLicense();
    expect($requests)->toHaveCount(2);

    // And the version answer is now asked for again rather than served from yesterday's no.
    $unlicensed->version();
    expect($requests)->toHaveCount(3);
    expect(sentBody($requests, 2)['edd_action'])->toBe('get_version');
});
