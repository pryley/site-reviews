<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Response;
use WP_UnitTestCase;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

class GeolocationTest extends WP_UnitTestCase
{
    public function set_up()
    {
        parent::set_up();
        delete_transient(Geolocation::RATE_LIMIT_KEY);
        delete_transient(GeolocateReviews::LOCK_KEY);
        delete_transient(GeolocateReviews::RETRY_KEY);
    }

    public function test_check_rate_limits_does_not_block()
    {
        set_transient(Geolocation::RATE_LIMIT_KEY, [
            'remaining' => 0,
            'reset_time' => time() + 30,
        ], 30);
        $method = $this->protectedMethod(Geolocation::class, 'checkRateLimits');
        $geolocation = $this->unconstructed(Geolocation::class);
        $start = microtime(true);
        $result = $method->invoke($geolocation);
        $this->assertTrue($result, 'rate limit in effect should be reported');
        $this->assertLessThan(5, microtime(true) - $start, 'checkRateLimits must not sleep out the reset window');
        delete_transient(Geolocation::RATE_LIMIT_KEY);
        $this->assertFalse($method->invoke($geolocation), 'no rate limit should be reported without a transient');
    }

    public function test_failed_batch_is_retried_then_aborted()
    {
        set_transient(GeolocateReviews::LOCK_KEY, true, 60);
        $command = glsr(GeolocateReviews::class);
        $method = $this->protectedMethod(GeolocateReviews::class, 'retryBatch');
        $response = new Response(new \WP_Error('http_request_failed', 'cURL error 28'));
        foreach (range(1, GeolocateReviews::MAX_RETRIES) as $attempt) {
            $method->invoke($command, 100, $response);
            $this->assertSame($attempt, (int) get_transient(GeolocateReviews::RETRY_KEY));
            $this->assertNotFalse(get_transient(GeolocateReviews::LOCK_KEY), 'lock should be kept while retrying');
        }
        $method->invoke($command, 100, $response); // exceeds MAX_RETRIES
        $this->assertFalse(get_transient(GeolocateReviews::RETRY_KEY), 'retry count should reset on abort');
        $this->assertFalse(get_transient(GeolocateReviews::LOCK_KEY), 'lock should be released on abort');
    }

    public function test_failed_response_does_not_trigger_rate_limit()
    {
        $method = $this->protectedMethod(Geolocation::class, 'handleRateLimits');
        $geolocation = $this->unconstructed(Geolocation::class);
        $response = new Response(new \WP_Error('http_request_failed', 'cURL error 28'));
        $method->invoke($geolocation, $response); // headers are an empty dictionary
        $this->assertFalse(get_transient(Geolocation::RATE_LIMIT_KEY), 'a failed request must not be mistaken for rate limiting');
    }

    public function test_rate_limit_headers_are_handled()
    {
        $method = $this->protectedMethod(Geolocation::class, 'handleRateLimits');
        $geolocation = $this->unconstructed(Geolocation::class);
        $response = new Response();
        $response->headers = new CaseInsensitiveDictionary([
            'x-rl' => '0',
            'x-ttl' => '10',
        ]);
        $method->invoke($geolocation, $response);
        $transient = get_transient(Geolocation::RATE_LIMIT_KEY);
        $this->assertSame(0, $transient['remaining']);
        $this->assertGreaterThan(time(), $transient['reset_time']);
    }

    protected function protectedMethod(string $className, string $method): \ReflectionMethod
    {
        $reflection = new \ReflectionMethod($className, $method);
        $reflection->setAccessible(true);
        return $reflection;
    }

    protected function unconstructed(string $className): object
    {
        return (new \ReflectionClass($className))->newInstanceWithoutConstructor();
    }
}
