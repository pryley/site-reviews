<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\GeolocationDefaults;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Geolocation
{
    public const API_URL = 'http://ip-api.com';

    public const FIELDS = [
        'city',
        'continentCode',
        'countryCode',
        'isp',
        'message',
        'query',
        'region',
        'status',
    ];

    /**
     * Transient key for rate limit tracking.
     */
    public const RATE_LIMIT_KEY = 'glsr_ip_api_rate_limit';

    /**
     * Rate limit safety buffer in seconds.
     */
    public const RATE_LIMIT_SAFETY_BUFFER = 5;

    protected Api $api;

    public function __construct()
    {
        $this->api = glsr(Api::class, ['url' => static::API_URL]);
    }

    public function batchLookup(array $ipaddresses): Response
    {
        $data = array_values(array_filter(array_map(
            [glsr(Sanitizer::class), 'sanitizeIpAddress'],
            $ipaddresses
        )));
        if (empty($data)) {
            return new Response();
        }
        if ($this->checkRateLimits()) {
            return new Response(); // rate limited, caller should retry later
        }
        $path = sprintf('/batch?fields=%s', implode(',', static::FIELDS));
        $response = $this->api->post($path, [
            'blocking' => true,
            'body' => wp_json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 15,
        ]);
        $this->handleRateLimits($response);
        if ($response->successful()) {
            $body = $response->body();
            $response->body = array_map([glsr(GeolocationDefaults::class), 'unguardedRestrict'], $body);
        }
        return $response;
    }

    public function lookup(string $ipOrDomain, bool $allowDomain = false): Response
    {
        $entity = glsr(Sanitizer::class)->sanitizeIpAddress($ipOrDomain) ?: (
            $allowDomain ? filter_var($ipOrDomain, \FILTER_VALIDATE_DOMAIN, \FILTER_FLAG_HOSTNAME) : false
        );
        if (empty($entity)) {
            return new Response();
        }
        if ($this->checkRateLimits()) {
            return new Response(); // rate limited, caller should retry later
        }
        $path = sprintf('/json/%s?fields=%s', $entity, implode(',', static::FIELDS));
        $response = $this->api->get($path, [
            'blocking' => true,
            'max_retries' => 1,
            'timeout' => 10,
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
     * Returns true if the rate limit is currently in effect.
     */
    protected function checkRateLimits(): bool
    {
        $transient = get_transient(static::RATE_LIMIT_KEY);
        if ($transient && 0 === $transient['remaining']) {
            $waitTime = max(0, $transient['reset_time'] - time());
            if ($waitTime > 0) {
                glsr_log()->warning("Geolocation: Rate limit reached, {$waitTime} seconds until reset");
                return true;
            }
        }
        return false;
    }

    /**
     * Handle rate limits based on transient and response headers.
     *
     * `/json` GET requests are limited to 45 requests per minute.
     * `/batch` POST requests are limited to 15 requests per minute.
     */
    protected function handleRateLimits(Response $response): void
    {
        if (!isset($response->headers['x-rl'])) {
            return; // headers are missing when the request failed
        }
        $remainingRequests = (int) $response->headers['x-rl'];
        $resetTime = (int) $response->headers['x-ttl'] + static::RATE_LIMIT_SAFETY_BUFFER;
        set_transient(static::RATE_LIMIT_KEY, [
            'remaining' => $remainingRequests,
            'reset_time' => time() + $resetTime,
        ], $resetTime);
    }
}
