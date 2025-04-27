<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\GeolocationDefaults;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Geolocation
{
    public const FIELDS = [
        'city',
        'continentCode',
        'countryCode',
        'message',
        'query',
        'region',
        'status',
    ];

    public const API_URL = 'http://ip-api.com';

    /**
     * String transient key for rate limit tracking.
     */
    public const RATE_LIMIT_KEY = 'glsr_ip_api_rate_limit';

    protected Api $api;

    public function __construct()
    {
        $this->api = glsr(Api::class, ['url' => static::API_URL]);
    }

    public function batchLookup(array $ipaddresses): Response
    {
        $this->checkRateLimits();
        $data = array_map([glsr(Sanitizer::class), 'sanitizeIpAddress'], $ipaddresses);
        $path = sprintf('/batch?fields=%s', implode(',', static::FIELDS));
        $response = $this->api->post($path, [
            'blocking' => true,
            'body' => wp_json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'max_retries' => 3,
            'timeout' => 15,
        ]);
        $this->handleRateLimits($response);
        if ($response->successful()) {
            $body = $response->body();
            $response->body = array_map([glsr(GeolocationDefaults::class), 'unguardedRestrict'], $body);
        }
        return $response;
    }

    public function lookup(string $ipaddress): Response
    {
        $this->checkRateLimits();
        $data = [
            'fields' => implode(',', static::FIELDS),
        ];
        $path = sprintf('/json/%s', glsr(Sanitizer::class)->sanitizeIpAddress($ipaddress));
        $response = $this->api->get($path, [
            'blocking' => true,
            'body' => $data,
            'max_retries' => 3,
            'timeout' => 15,
        ]);
        $this->handleRateLimits($response);
        if ($response->successful()) {
            $body = $response->body();
            $response->body = glsr(GeolocationDefaults::class)->unguardedRestrict($body);
        }
        return $response;
    }

    /**
     * Check rate limits based on transient.
     */
    protected function checkRateLimits(): void
    {
        $transient = get_transient(static::RATE_LIMIT_KEY);
        if ($transient && 0 === $transient['remaining']) {
            $waitTime = max(0, $transient['reset_time'] - time());
            if ($waitTime > 0) {
                glsr_log()->warning("Geolocation: Rate limit reached, waiting {$waitTime} seconds");
                sleep($waitTime);
            }
        }
    }

    /**
     * Handle rate limits based on transient and response headers.
     *
     * `/json` GET requests are limited to 45 requests per minute.
     * `/batch` POST requests are limited to 15 requests per minute.
     */
    protected function handleRateLimits(Response $response): void
    {
        $remainingRequests = (int) $response->headers['x-rl'];
        $resetTime = (int) $response->headers['x-ttl'] + 5; // Add an extra 5 seconds just in case
        set_transient(static::RATE_LIMIT_KEY, [
            'remaining' => $remainingRequests,
            'reset_time' => time() + $resetTime,
        ], $resetTime);
    }
}
