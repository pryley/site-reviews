<?php

use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Response;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

use function GeminiLabs\SiteReviews\Tests\protectedMethod;
use function GeminiLabs\SiteReviews\Tests\unconstructed;

// The per-test transaction already rolls the transients back (they live in the options table),
// but a test that asserts on their absence should not depend on that.
beforeEach(function () {
    delete_transient(Geolocation::RATE_LIMIT_KEY);
    delete_transient(GeolocateReviews::LOCK_KEY);
    delete_transient(GeolocateReviews::RETRY_KEY);
});

test('check rate limits does not block', function () {
    set_transient(Geolocation::RATE_LIMIT_KEY, [
        'remaining' => 0,
        'reset_time' => time() + 30,
    ], 30);
    $method = protectedMethod(Geolocation::class, 'checkRateLimits');
    $geolocation = unconstructed(Geolocation::class);
    $start = microtime(true);
    $result = $method->invoke($geolocation);
    $this->assertTrue($result, 'rate limit in effect should be reported');
    $this->assertLessThan(5, microtime(true) - $start, 'checkRateLimits must not sleep out the reset window');
    delete_transient(Geolocation::RATE_LIMIT_KEY);
    $this->assertFalse($method->invoke($geolocation), 'no rate limit should be reported without a transient');
});

test('failed batch is retried then aborted', function () {
    set_transient(GeolocateReviews::LOCK_KEY, true, 60);
    $command = glsr(GeolocateReviews::class);
    $method = protectedMethod(GeolocateReviews::class, 'retryBatch');
    $response = new Response(new \WP_Error('http_request_failed', 'cURL error 28'));
    foreach (range(1, GeolocateReviews::MAX_RETRIES) as $attempt) {
        $method->invoke($command, 100, $response);
        expect($attempt)->toBe((int) get_transient(GeolocateReviews::RETRY_KEY));
        $this->assertNotFalse(get_transient(GeolocateReviews::LOCK_KEY), 'lock should be kept while retrying');
    }
    $method->invoke($command, 100, $response); // exceeds MAX_RETRIES
    $this->assertFalse(get_transient(GeolocateReviews::RETRY_KEY), 'retry count should reset on abort');
    $this->assertFalse(get_transient(GeolocateReviews::LOCK_KEY), 'lock should be released on abort');
});

test('failed response does not trigger rate limit', function () {
    $method = protectedMethod(Geolocation::class, 'handleRateLimits');
    $geolocation = unconstructed(Geolocation::class);
    $response = new Response(new \WP_Error('http_request_failed', 'cURL error 28'));
    $method->invoke($geolocation, $response); // headers are an empty dictionary
    $this->assertFalse(get_transient(Geolocation::RATE_LIMIT_KEY), 'a failed request must not be mistaken for rate limiting');
});

test('rate limit headers are handled', function () {
    $method = protectedMethod(Geolocation::class, 'handleRateLimits');
    $geolocation = unconstructed(Geolocation::class);
    $response = new Response();
    $response->headers = new CaseInsensitiveDictionary([
        'x-rl' => '0',
        'x-ttl' => '10',
    ]);
    $method->invoke($geolocation, $response);
    $transient = get_transient(Geolocation::RATE_LIMIT_KEY);
    expect($transient['remaining'])->toBe(0);
    expect($transient['reset_time'])->toBeGreaterThan(time());
});
