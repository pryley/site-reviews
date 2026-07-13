<?php

use GeminiLabs\SiteReviews\Addons\Updater;
use GeminiLabs\SiteReviews\Controllers\UpdateController;

use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * Telling WordPress an addon has an update.
 *
 * WordPress asks, through `update_plugins_{hostname}`, and whatever this returns is what the
 * Plugins screen shows and what the one-click updater installs — because the answer carries a
 * `package`, which is a URL WordPress downloads a zip from and unpacks over the top of the
 * running addon.
 *
 * So the two things worth being certain of are opposites of each other:
 *
 *   a real update must be offered — somebody paid for it, and an addon that never updates is
 *   an addon with unpatched bugs in it;
 *   and NOTHING must be offered when the answer did not come back. A licence server that is
 *   down, an expired licence, a 500, a timeout — none of them may turn into an update, because
 *   an update with an empty `package` is a plugin screen that offers a broken download, and an
 *   update invented out of a failed request is worse than that.
 *
 * The throttle matters too, and is easy to overlook: without it the licence server would be
 * asked on EVERY admin page load, for every addon, on every site.
 */

beforeEach(function () {
    resetPluginState();
    delete_site_option(glsr()->prefix.'last_checked_site-reviews-images');
});

function updateController(): UpdateController
{
    return glsr(UpdateController::class);
}

/**
 * The plugin header WordPress hands to the update filter.
 */
function addonPluginData(array $overrides = []): array
{
    return array_replace([
        'PluginURI' => 'https://niftyplugins.com/plugin/site-reviews-images/',
        'TextDomain' => 'site-reviews-images',
        'UpdateURI' => 'https://updates.example.org',
        'Version' => '1.0.0',
    ], $overrides);
}

function versionReply(array $body): array
{
    return ['body' => (string) wp_json_encode($body)];
}

/*
 * When there is an update.
 */

test('an available update is offered, with the package wordpress will install', function () {
    interceptHttp(versionReply([
        'new_version' => '2.0.0',
        'package' => 'https://updates.example.org/download/images.zip',
        'requires_php' => '8.1',
        'slug' => 'site-reviews-images',
        'version' => '2.0.0',
    ]));

    $update = updateController()->filterUpdatePlugins(false, addonPluginData());

    expect($update['version'])->toBe('2.0.0')
        ->and($update['package'])->toBe('https://updates.example.org/download/images.zip')
        ->and($update['slug'])->toBe('site-reviews-images')
        ->and($update['requires_php'])->toBe('8.1');
});

/*
 * When there is not. Every one of these must hand back what WordPress already had.
 */

test('a licence server that is down does not become an update', function () {
    // THE ONE THAT MATTERS. `false` is what WordPress passed in, and `false` is what it gets
    // back: no update. An empty `package` on the Plugins screen is a broken download button.
    add_filter('site-reviews/api/args', fn ($args) => array_replace($args, ['max_retries' => 1]));
    interceptHttp(['response' => ['code' => 503, 'message' => 'Service Unavailable']]);

    expect(updateController()->filterUpdatePlugins(false, addonPluginData()))->toBeFalse();
});

test('an expired licence does not become an update', function () {
    // The server answers, and politely declines: no version, no package.
    interceptHttp(versionReply(['msg' => 'Your license key has expired.']));

    expect(updateController()->filterUpdatePlugins(false, addonPluginData()))->toBeFalse();
});

test('an update wordpress already knew about is not thrown away', function () {
    // The filter is handed whatever the last plugin said. When we have nothing to add, we must
    // hand it back rather than answer for everybody.
    interceptHttp(versionReply([]));
    $existing = ['package' => 'https://example.org/other.zip', 'version' => '9.9.9'];

    expect(updateController()->filterUpdatePlugins($existing, addonPluginData()))->toBe($existing);
});

/*
 * The message on the Plugins screen when there is an update but no way to get it.
 */

test('an update with no package tells the person they need a licence', function () {
    // An addon whose licence has lapsed still gets told a new version exists — it just cannot
    // download it. Saying nothing here would leave "there is an update" next to a button that
    // does nothing, which is how support tickets are made.
    ob_start();
    updateController()->renderPluginUpdateMessage(addonPluginData(), (object) ['package' => '']);
    $message = (string) ob_get_clean();

    expect($message)->toContain('license key')
        ->and($message)->toContain('https://niftyplugins.com/plugin/site-reviews-images/');
});

test('an update that CAN be installed says nothing extra', function () {
    ob_start();
    updateController()->renderPluginUpdateMessage(addonPluginData(), (object) [
        'package' => 'https://updates.example.org/download/images.zip',
    ]);

    expect((string) ob_get_clean())->toBe('');
});

/*
 * The plugin-information modal.
 */

test('the modal is only answered for our own addons', function () {
    // `plugins_api` is asked about every plugin on wordpress.org. Answering for somebody else's
    // would replace their modal with ours.
    $data = (object) ['name' => 'Somebody Else'];

    expect(updateController()->filterPluginsApi($data, 'plugin_information', (object) ['slug' => 'akismet']))
        ->toBe($data);
    expect(updateController()->filterPluginsApi($data, 'query_plugins', (object) ['slug' => 'site-reviews-images']))
        ->toBe($data); // …and only for the plugin_information action
    expect(updateController()->filterPluginsApi($data, 'plugin_information', (object) ['slug' => '']))
        ->toBe($data); // …and not for no slug at all
});

/*
 * The throttle.
 */

test('the licence server is not asked again on the next page load', function () {
    // Without this, every admin page load would cost one HTTP request per addon — on a site
    // with six of them, that is six round trips to niftyplugins.com before the dashboard draws.
    $requests = interceptHttp(versionReply(['new_version' => '2.0.0', 'version' => '2.0.0']));

    updateController()->filterUpdatePlugins(false, addonPluginData());
    updateController()->filterUpdatePlugins(false, addonPluginData());

    // The second call did not force a fresh check, so it was answered from the cached response
    // rather than by asking again.
    expect($requests)->toHaveCount(1);
});

test('an addon that has never been checked is checked', function () {
    $requests = interceptHttp(versionReply(['new_version' => '2.0.0', 'version' => '2.0.0']));

    updateController()->filterUpdatePlugins(false, addonPluginData());

    expect($requests)->toHaveCount(1);
    expect((int) get_site_option(glsr()->prefix.'last_checked_site-reviews-images'))
        ->toBeGreaterThan(0); // and the clock started
});
