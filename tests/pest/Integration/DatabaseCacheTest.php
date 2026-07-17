<?php

use GeminiLabs\SiteReviews\Database\Cache;

use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The Cache facade over wp_cache/transients: the lazy get-with-callback, the
 * rollback-version list from the wordpress.org API, the remote-POST probe, and
 * the WP_Debug_Data snapshot that a broken third party must not take down.
 */

beforeEach(function () {
    resetPluginState();
    delete_transient(glsr()->prefix.'rollback_versions');
    delete_transient(glsr()->prefix.'remote_post_test');
    delete_transient(glsr()->prefix.'system_info');
});

test('a cache miss runs the callback once and stores what it returned', function () {
    $calls = new ArrayObject();
    $callback = function () use ($calls) {
        $calls->append(1);
        return 'computed-value';
    };

    expect(glsr(Cache::class)->get('a-key', 'a-group', $callback))->toBe('computed-value')
        ->and(glsr(Cache::class)->get('a-key', 'a-group', $callback))->toBe('computed-value')
        ->and($calls)->toHaveCount(1); // the second get was served from the cache

    glsr(Cache::class)->delete('a-key', 'a-group');
    expect(glsr(Cache::class)->get('a-key', 'a-group'))->toBeFalse(); // no callback: a plain miss
});

test('the rollback versions come from the api once, then from the transient', function () {
    // The API is faked through the plugins_api short-circuit filter; the version and
    // trunk are removed, the newest ten are kept, and the latest release of the
    // PREVIOUS major is appended so a rollback across majors is always offered.
    $major = (int) glsr()->version('major');
    $prevMajor = $major - 1;
    $versions = ['trunk' => 'trunk.zip', glsr()->version => 'current.zip', "{$prevMajor}.5.0" => 'prev.zip'];
    for ($i = 0; $i < 11; ++$i) {
        $versions["{$major}.0.{$i}"] = "v{$i}.zip";
    }
    add_filter('plugins_api', fn () => (object) ['versions' => $versions]);
    try {
        $result = glsr(Cache::class)->getPluginVersions();

        expect($result)->toHaveCount(11) // ten newest + the previous-major release
            ->and($result)->toContain("{$prevMajor}.5.0")
            ->and($result)->not->toContain('trunk')
            ->and($result)->not->toContain(glsr()->version)
            ->and($result)->toContain("{$major}.0.10")
            ->and($result)->not->toContain("{$major}.0.0"); // the oldest fell off the top-ten

        // now cached: the api is not asked again
        add_filter('plugins_api', fn () => (object) ['versions' => []], 5);
        expect(glsr(Cache::class)->getPluginVersions())->toBe($result);
    } finally {
        remove_all_filters('plugins_api');
    }
});

test('an api error, or an api with no versions, is logged and yields no versions', function () {
    add_filter('plugins_api', fn () => new WP_Error('http_error', 'API unreachable'));
    try {
        expect(glsr(Cache::class)->getPluginVersions())->toBe([]);
    } finally {
        remove_all_filters('plugins_api');
    }

    add_filter('plugins_api', fn () => (object) ['versions' => []]);
    try {
        expect(glsr(Cache::class)->getPluginVersions())->toBe([]);
        expect(get_transient(glsr()->prefix.'rollback_versions'))->toBeFalse(); // failures are not cached
    } finally {
        remove_all_filters('plugins_api');
    }
});

test('the remote post probe reports Works for a 2xx and remembers the answer', function () {
    add_filter('pre_http_request', fn () => ['response' => ['code' => 200], 'body' => '', 'headers' => []]);
    try {
        expect(glsr(Cache::class)->getRemotePostTest())->toBe('Works');
    } finally {
        remove_all_filters('pre_http_request');
    }

    // cached in a transient: no request happens now (an unfaked request would fail here)
    expect(glsr(Cache::class)->getRemotePostTest())->toBe('Works');

    delete_transient(glsr()->prefix.'remote_post_test');
    add_filter('pre_http_request', fn () => new WP_Error('http_failure', 'no network'));
    try {
        expect(glsr(Cache::class)->getRemotePostTest())->toBe('Does not work');
    } finally {
        remove_all_filters('pre_http_request');
    }
});

test('a third party fatally breaking the debug data yields an empty report, not a white screen', function () {
    // The catch exists for "badly made migration plugins". A hook callback with a wrong
    // signature throws a TypeError from inside WP_Debug_Data::debug_data(), and the
    // plugin's system info degrades to empty instead of taking the admin page down.
    $badCallback = fn (string $mustBeString) => $mustBeString; // receives an array
    add_filter('debug_information', $badCallback);
    try {
        expect(glsr(Cache::class)->getSystemInfo())->toBe([]);
        expect(get_transient(glsr()->prefix.'system_info'))->toBeFalse(); // failures are not cached
    } finally {
        remove_filter('debug_information', $badCallback);
    }

    // and with nobody misbehaving, the data is real and is cached
    $data = glsr(Cache::class)->getSystemInfo();
    expect($data)->toHaveKey('wp-core')
        ->and(get_transient(glsr()->prefix.'system_info'))->not->toBeFalse();
});
