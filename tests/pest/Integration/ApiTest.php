<?php

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Response;

use function GeminiLabs\SiteReviews\Tests\interceptHttp;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The HTTP client every paid addon depends on.
 *
 * It is the one piece of the plugin that talks to a machine that is not the site's own,
 * and it is on the path of things people have paid for: activating a licence, checking a
 * licence, being offered an update. When it is wrong, an addon somebody bought stops
 * updating, and the only evidence is a line in a console nobody reads.
 *
 * Three things it does, and all three are asserted here:
 *
 *   caches       a successful response goes into a SITE transient keyed by url + body, so
 *                that a licence check on every admin page load is one request a day and
 *                not one per page. `force` is how a caller says "no, really, ask".
 *   retries      a 429 or a 5xx is the server saying "not now" rather than "no", and the
 *                client is supposed to back off and ask again.
 *   parses       whatever comes back is turned into a Response: JSON if it is JSON, and
 *                a WP_Error into an error rather than an exception.
 *
 * Nothing leaves the container: blockHttpRequests() (bootstrap.php) fails any request a
 * test has not deliberately intercepted, and interceptHttp() records the ones it has.
 */

beforeEach(function () {
    resetPluginState();
});

function api(string $url = 'https://api.example.org/v1/'): Api
{
    return new Api($url);
}

/**
 * A canned HTTP reply with a status code of our choosing.
 */
function httpReply(int $code, array $body = [], string $raw = null): array
{
    return [
        'body' => $raw ?? (string) wp_json_encode($body),
        'response' => ['code' => $code, 'message' => get_status_header_desc($code)],
    ];
}

/*
 * Where the request goes.
 */

test('a path is appended to the base url, and the whole thing is sanitized', function () {
    expect(api()->url('licenses'))->toBe('https://api.example.org/v1/licenses')
        ->and(api()->url('/licenses'))->toBe('https://api.example.org/v1/licenses') // a leading slash is not a second one
        ->and(api()->url(''))->toBe('https://api.example.org/v1'); // and no path is the base itself
});

test('the default base url is the plugin api', function () {
    expect((new Api())->url(''))->toBe('https://api.site-reviews.com/v1');
});

/*
 * The cache, which is what stops a licence check happening on every page load.
 */

test('a successful response is cached, and the second caller does not leave the building', function () {
    $requests = interceptHttp(httpReply(200, ['license' => 'valid']));

    $first = api()->get('check', ['transient_key' => 'check_license']);
    $second = api()->get('check', ['transient_key' => 'check_license']);

    expect($first->body())->toBe(['license' => 'valid'])
        ->and($second->body())->toBe(['license' => 'valid'])
        ->and($requests)->toHaveCount(1); // the second was answered from the transient
});

test('force asks again even when the answer is already cached', function () {
    // What the Updater does before every licence call: an answer cached a day ago is no
    // use to somebody who has just pasted in a new licence key.
    $requests = interceptHttp(httpReply(200, ['license' => 'valid']));

    api()->get('check', ['transient_key' => 'check_license']);
    api()->get('check', ['transient_key' => 'check_license', 'force' => true]);

    expect($requests)->toHaveCount(2);
});

test('a failed response is not cached', function () {
    // Caching a failure for a day would mean a site that was briefly offline stops being
    // offered updates until tomorrow.
    $requests = interceptHttp(httpReply(404));

    api()->get('check', ['transient_key' => 'check_license']);
    api()->get('check', ['transient_key' => 'check_license']);

    expect($requests)->toHaveCount(2);
});

test('two different request bodies are cached separately', function () {
    // The transient key is hashed from the url AND the body, so a licence check for one
    // addon cannot be answered with the cached reply for another.
    $requests = interceptHttp(httpReply(200, ['license' => 'valid']));
    $args = fn (string $addon) => [
        'body' => ['item_name' => $addon],
        'transient_key' => 'check_license',
    ];

    api()->post('/', $args('site-reviews-images'));
    api()->post('/', $args('site-reviews-forms'));

    expect($requests)->toHaveCount(2);

    $keys = [
        api()->transientKey('/', 'check_license', ['item_name' => 'site-reviews-images']),
        api()->transientKey('/', 'check_license', ['item_name' => 'site-reviews-forms']),
    ];
    expect($keys[0])->not->toBe($keys[1])
        ->and($keys[0])->toStartWith(glsr()->prefix.'api_check_license_');
});

test('the transient key does not carry the url or the body in the clear', function () {
    $key = api()->transientKey('/', 'check_license', ['license' => 'a-real-licence-key']);

    expect($key)->not->toContain('a-real-licence-key')
        ->and($key)->not->toContain('example.org');
});

/*
 * The retries. A 429 or a 5xx is "ask me again", and the client has a whole backoff
 * schedule for doing so — BACKOFF_INITIAL, BACKOFF_MULTIPLIER, BACKOFF_MAX, the jittered
 * deadline, wait(). None of it is any use if the loop returns before it gets there.
 */

test('a server error is retried, one time more than max_retries', function () {
    // max_retries is the number of RETRIES, not the number of attempts — so the total is one
    // MORE than it: the first try, then max_retries more. Two retries here, so three requests.
    // Two rather than the default of nought, and rather than a larger number, only to keep the
    // test quick: the backoff sleeps for about a second before each retry.
    $requests = interceptHttp(httpReply(503));

    $response = api()->get('check', ['max_retries' => 2, 'transient_key' => 'check_license']);

    expect($requests)->toHaveCount(3) // one attempt + two retries
        ->and($response->code)->toBe(503); // and the caller is told what the server said
});

test('a rate limit is retried', function () {
    $requests = interceptHttp(httpReply(429));

    api()->get('check', ['max_retries' => 2, 'transient_key' => 'check_license']);

    expect($requests)->toHaveCount(3); // one attempt + two retries
});

test('no retries means a single attempt', function () {
    // The floor, and the default. max_retries => 0 is one attempt and no retries — and it hands
    // back what the server actually said. It used to make a second request here and then throw
    // that away for a WP_Error it had invented.
    $requests = interceptHttp(httpReply(503));

    $response = api()->get('check', ['max_retries' => 0, 'transient_key' => 'check_license']);

    expect($requests)->toHaveCount(1)
        ->and($response->code)->toBe(503)
        ->and($response->error)->toBeFalse(); // a real response, not a manufactured failure
});

test('a not-found is not retried, because asking again will not help', function () {
    $requests = interceptHttp(httpReply(404));

    api()->get('check', ['max_retries' => 2, 'transient_key' => 'check_license']);

    expect($requests)->toHaveCount(1);
});

test('a 429 and a 5xx are the only things worth asking again about', function () {
    $response = fn (int $code) => new Response(httpReply($code));

    expect($response(429)->shouldRetry())->toBeTrue()
        ->and($response(500)->shouldRetry())->toBeTrue()
        ->and($response(503)->shouldRetry())->toBeTrue()
        ->and($response(404)->shouldRetry())->toBeFalse()
        ->and($response(401)->shouldRetry())->toBeFalse() // a bad licence key is not a hiccup
        ->and($response(200)->shouldRetry())->toBeFalse();
});

/*
 * What comes back.
 */

test('a json body is decoded, and anything else is handed back whole', function () {
    $json = new Response(httpReply(200, ['license' => 'valid']));
    expect($json->body())->toBe(['license' => 'valid'])
        ->and($json->successful())->toBeTrue()
        ->and($json->failed())->toBeFalse();

    // An HTML error page from a proxy, say. It is not thrown away and it is not fatal —
    // it lands in `result`, where whoever is reading the console can see it.
    $html = new Response(httpReply(502, [], '<html>Bad Gateway</html>'));
    expect($html->body())->toBe(['result' => '<html>Bad Gateway</html>'])
        ->and($html->successful())->toBeFalse();
});

test('a wp_error becomes a failed response rather than an exception', function () {
    // This is the offline site: no DNS, no route, a firewall. wp_remote_request() hands
    // back a WP_Error and the plugin has to carry on.
    $response = new Response(new WP_Error('http_request_failed', 'Could not resolve host'));

    expect($response->error)->toBeTrue()
        ->and($response->code)->toBe(0)
        ->and($response->body())->toBe([])
        ->and($response->message)->toBe('Could not resolve host')
        ->and($response->failed())->toBeTrue()
        ->and($response->shouldRetry())->toBeFalse();
});

test('the data key is unwrapped, and serialized values in it are unserialized', function () {
    $response = new Response(httpReply(200, [
        'data' => ['sections' => serialize(['description' => 'A description'])],
    ]));

    expect($response->data())->toBe(['sections' => ['description' => 'A description']]);
});

/*
 * Flushing.
 */

test('flushing removes the cached answer, whatever body was asked with', function () {
    // THE ONE THAT MATTERS. flushCachedVersion() is called before every licence call, and
    // this is it: somebody has just activated the licence they paid for, and the cached
    // get_version from before they had one -- the one that says there is no update -- has
    // to go, or they are told there is nothing to download for a whole day.
    //
    // The body it was cached under contains the OLD licence, so the key cannot be
    // reconstructed. It is found by the index Api keeps of the keys it has set.
    $requests = interceptHttp(httpReply(200, ['new_version' => '1.0.0']));
    $unlicensed = ['body' => ['item_name' => 'site-reviews-images', 'license' => ''], 'transient_key' => 'get_version'];
    $licensed = ['body' => ['item_name' => 'site-reviews-images', 'license' => 'a-real-key'], 'transient_key' => 'get_version'];

    api()->post('/', $unlicensed);
    api()->post('/', $unlicensed);
    expect($requests)->toHaveCount(1); // cached

    api()->flushAll('get_version'); // the licence was just activated

    api()->post('/', $licensed);
    api()->post('/', $unlicensed);
    expect($requests)->toHaveCount(3); // both asked again; neither was answered from the cache
});

test('flushing one key leaves the others alone', function () {
    // A licence check must not throw away the cached version response for every OTHER addon
    // on the site.
    $requests = interceptHttp(httpReply(200, ['ok' => true]));
    $version = ['transient_key' => 'get_version'];
    $license = ['transient_key' => 'check_license'];

    api()->get('a', $version);
    api()->get('b', $license);
    expect($requests)->toHaveCount(2);

    api()->flushAll('get_version', 'a'); // the key is hashed from the url too, so it is named

    api()->get('a', $version);  // flushed, so asked again
    api()->get('b', $license);  // still cached
    expect($requests)->toHaveCount(3);
});

test('the index does not outlive the things it indexes', function () {
    // It is an option, not a transient, so it has to be tidied up by hand or it grows
    // forever on a site that changes its licence key a few times.
    $index = fn () => (array) get_site_option(glsr()->prefix.'api_transients', []);
    interceptHttp(httpReply(200, ['ok' => true]));

    api()->get('check', ['transient_key' => 'check_license']);
    expect($index())->toHaveCount(1);

    api()->flushAll('check_license', 'check');
    expect($index())->toBe([]); // and the option is gone with it
});

test('a targeted flush forgets exactly one answer', function () {
    $requests = interceptHttp(httpReply(200, ['ok' => true]));
    $body = ['item_name' => 'site-reviews-images'];

    api()->post('/', ['body' => $body, 'transient_key' => 'get_version']);
    api()->flush('get_version', '/', $body);
    api()->post('/', ['body' => $body, 'transient_key' => 'get_version']);

    expect($requests)->toHaveCount(2);
});

/*
 * TLS.
 *
 * This channel carries a licence key up, and a `package` URL down — the URL WordPress then
 * downloads a zip from and installs. It is the last channel on the site that should be
 * talking to a server it has not checked the identity of.
 *
 * ApiDefaults sets `sslverify` to Helper::isLocalServer(), and isLocalServer() is TRUE on a
 * developer's machine and FALSE on a real site. `is-local-server` is a documented filter, so
 * both halves can be driven from here.
 */

test('a real site verifies the certificate of the licence server', function () {
    $requests = interceptHttp(httpReply(200));
    add_filter('site-reviews/is-local-server', '__return_false');

    api()->get('check');

    expect($requests[0]['args']['sslverify'])->toBeTrue();
});

test('a local site does not, because its certificate is its own', function () {
    // The case the setting exists for: a .test or .local host with a self-signed
    // certificate, where verifying would fail every request.
    $requests = interceptHttp(httpReply(200));
    add_filter('site-reviews/is-local-server', '__return_true');

    api()->get('check');

    expect($requests[0]['args']['sslverify'])->toBeFalse();
});

test('the request args can be filtered', function () {
    // A site behind a corporate proxy with its own certificate authority needs to be able
    // to reach the licence server too.
    $requests = interceptHttp(httpReply(200));
    add_filter('site-reviews/api/args', function ($args) {
        $args['timeout'] = 30;

        return $args;
    });

    api()->get('check');

    expect($requests[0]['args']['timeout'])->toBe(30);
});
