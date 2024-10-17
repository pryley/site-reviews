<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\ApiDefaults;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Api
{
    protected const BACKOFF_INITIAL = 1.0;
    protected const BACKOFF_MAX = 120.0;
    protected const BACKOFF_JITTER = 0.23;
    protected const BACKOFF_MULTIPLIER = 1.6;
    protected const DEFAULT_BASE_URL = 'https://api.site-reviews.com/v1/';

    public string $baseUrl;

    protected int $numRetries = 0;
    protected float $backoff;
    protected float $deadline;

    public function __construct(string $url = '')
    {
        $this->backoff = self::BACKOFF_INITIAL;
        $this->baseUrl = trailingslashit($url ?: static::DEFAULT_BASE_URL);
        $this->deadline = microtime(true) + $this->backoff;
    }

    public function args(array $args = []): array
    {
        $args = glsr(ApiDefaults::class)->merge($args);
        $args = glsr()->filterArray('api/args', $args, $this->baseUrl);
        return $args;
    }

    public function flush(string $transientKey, string $path = '', array $body = []): void
    {
        $transient = $this->transientKey($path, $transientKey, $body);
        delete_site_transient($transient);
    }

    public function flushAll(string $transientKey, string $path = ''): void
    {
        $transient = $this->transientKey($path, $transientKey);
        $transient = "_site_transient_{$transient}%";
        $sql = "
            SELECT option_name
            FROM table|options
            WHERE option_name LIKE %s
        ";
        $transientKeys = glsr(Database::class)->dbGetCol(
            glsr(Query::class)->sql($sql, $transient)
        );
        foreach ($transientKeys as $transient) {
            delete_site_transient($transient);
        }
    }

    public function get(string $path, array $args = []): Response
    {
        return $this->request($path, wp_parse_args(['method' => 'GET'], $args));
    }

    public function post(string $path, array $args = []): Response
    {
        return $this->request($path, wp_parse_args(['method' => 'POST'], $args));
    }

    public function request(string $path, array $args = []): Response
    {
        $args = $this->args($args);
        $body = glsr(Sanitizer::class)->sanitizeJson($args['body'] ?: []);
        $transientKey = $this->transientKey($path, $args['transient_key'], $body);
        if ($args['force']) {
            delete_site_transient($transientKey);
        }
        $result = get_site_transient($transientKey);
        if (!empty($result)) {
            return new Response($result);
        }
        $this->numRetries = 0;
        while ($this->numRetries <= $args['max_retries']) {
            $nextRetry = $this->numRetries + 1;
            $timeout = max($args['timeout'], $this->timeUntilDeadline());
            $url = $this->url($path);
            $result = wp_remote_request($url, wp_parse_args(compact('timeout'), $args));
            $response = new Response($result);
            if ($response->successful()) {
                set_site_transient($transientKey, $result, $args['expiration']);
                return $response;
            }
            if (!$response->shouldRetry()) {
                return $response;
            }
            if ($nextRetry < $args['max_retries']) {
                return $response;
            }
            $this->wait();
            glsr_log("Starting retry {$nextRetry} for {$url} after sleeping for {$this->timeUntilDeadline()} seconds.");
        }
        return new Response(new \WP_Error('', "API request failed after {$this->numRetries} attempts.")); // this should never be the result
    }

    public function transientKey(string $path = '', string $transientKey = 'request', array $body = []): string
    {
        if (empty($body)) {
            $bodyHash = '';
        } else {
            $bodyHash = Str::hash((string) maybe_serialize($body), 8);
        }
        $url = Str::hash($this->url($path), 8);
        $key = sanitize_key($transientKey);
        $key = trim("{$key}_{$url}_{$bodyHash}", '_');
        return glsr()->prefix."api_{$key}";
    }

    public function url(string $path): string
    {
        $path = ltrim($path, '/');
        $url = $this->baseUrl.$path;
        return glsr(Sanitizer::class)->sanitizeUrl($url);
    }

    /**
     * Apply multiplier to current backoff time.
     */
    protected function newBackoff(): float
    {
        return min($this->backoff * self::BACKOFF_MULTIPLIER, self::BACKOFF_MAX);
    }

    /**
     * Get deadline by applying jitter as a proportion of backoff:
     * if jitter is 0.1, then multiply backoff by random value in [0.9, 1.1]
     */
    protected function newDeadline(): float
    {
        $now = microtime(true);
        $jitter = 1 + self::BACKOFF_JITTER * (2 * (rand() / getrandmax()) - 1);
        return $now + $this->backoff * $jitter;
    }

    protected function timeUntilDeadline(): float
    {
        $now = microtime(true);
        return max($this->deadline - $now, 0.0);
    }

    /**
     * Note: usleep() with values larger than 1000000 (1 second) may not be supported by the operating system.
     */
    protected function wait(): void
    {
        $timeUntilDeadline = $this->timeUntilDeadline();
        if ($timeUntilDeadline > 1.0) {
            sleep((int) floor($timeUntilDeadline));
            $timeUntilDeadline = $this->timeUntilDeadline();
        }
        usleep((int) floor($timeUntilDeadline * 1e6));
        $this->backoff = $this->newBackoff();
        $this->deadline = $this->newDeadline();
        ++$this->numRetries;
    }
}
