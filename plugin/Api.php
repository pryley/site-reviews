<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Defaults\ApiDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Api
{
    protected const BACKOFF_INITIAL = 1.0;
    protected const BACKOFF_MAX = 120.0;
    protected const BACKOFF_JITTER = 0.23;
    protected const BACKOFF_MULTIPLIER = 1.6;
    protected const CONNECT_TIMEOUT = 5;
    protected const DEFAULT_BASE_URL = 'https://api.site-reviews.com/v1/';
    protected const UNREACHABLE_INITIAL = 15 * \MINUTE_IN_SECONDS;
    protected const UNREACHABLE_MAX = 6 * \HOUR_IN_SECONDS;

    public string $baseUrl;

    protected int $numRetries = 0;
    protected float $backoff;
    protected float $deadline;

    public function __construct(string $url = '')
    {
        $this->backoff = self::BACKOFF_INITIAL;
        $this->baseUrl = untrailingslashit($url ?: static::DEFAULT_BASE_URL);
        $this->deadline = microtime(true) + $this->backoff;
    }

    public function args(array $args = []): array
    {
        $args = glsr()->filterArray('api/args', $args, $this->baseUrl);
        $args = glsr(ApiDefaults::class)->merge($args);
        return $args;
    }

    public function flush(string $transientKey, string $path = '', array $body = []): void
    {
        delete_site_transient($this->transientKey($path, $transientKey, $body));
    }

    /**
     * Forgets every cached response for this transient key and path regardless of body.
     */
    public function flushAll(string $transientKey, string $path = ''): void
    {
        $lastFlushed = Arr::consolidate(get_site_option($this->lastFlushedKey()));
        $lastFlushed[$this->flushGroup($path, $transientKey)] = microtime();
        update_site_option($this->lastFlushedKey(), $lastFlushed);
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
        $body = glsr(Sanitizer::class)->sanitizeJson($args['body'] ?: []); // in case body is a JSON string
        $transientKey = $this->transientKey($path, $args['transient_key'], $body);
        $url = $this->url($path);
        if ($args['force']) {
            delete_site_transient($transientKey);
        } else {
            $result = $args['cache'] ? get_site_transient($transientKey) : false;
            if (!empty($result)) {
                return new Response($result);
            }
            $until = $args['skip_when_down'] ? $this->unreachableUntil() : 0;
            if ($until > time()) {
                return Response::unreachable($this->unreachableMessage($url, $until));
            }
        }
        $maxRetries = max(0, $args['max_retries']);
        $this->numRetries = 0;
        while (true) {
            $timeout = max($args['timeout'], $this->timeUntilDeadline());
            $result = $this->send($url, wp_parse_args(compact('timeout'), $args));
            $response = new Response($result);
            if ($response->successful()) {
                if ($args['cache']) {
                    set_site_transient($transientKey, $result, $args['expiration']);
                }
                if ($args['skip_when_down']) {
                    $this->markReachable();
                }
                return $response;
            }
            if (!$response->shouldRetry() || $this->numRetries >= $maxRetries) {
                if ($args['skip_when_down'] && ($response->error || $response->shouldRetry())) {
                    $this->markUnreachable($url); // no connection or "not now"; a 4xx is an answer
                }
                return $response; // "no" rather than "not now", or nothing left to try
            }
            $this->wait(); // increments numRetries
            glsr_log()->debug(sprintf(
                'Retrying %s (attempt %d of %d).', $url, $this->numRetries + 1, $maxRetries + 1
            ));
        }
    }

    public function transientKey(string $path = '', string $transientKey = 'request', array $body = []): string
    {
        if (empty($body)) {
            $bodyHash = '';
        } else {
            $bodyHash = Str::hash((string) maybe_serialize($body), 8);
        }
        $url = Str::hash($this->url($path).$this->lastFlushed($path, $transientKey), 8);
        $key = sanitize_key($transientKey);
        $key = trim("{$key}_{$url}_{$bodyHash}", '_');
        return glsr()->prefix."api_{$key}";
    }

    public function unreachableKey(): string
    {
        $host = (string) wp_parse_url($this->baseUrl, \PHP_URL_HOST);
        return glsr()->prefix.'api_unreachable_'.Str::hash($host, 8);
    }

    public function url(string $path): string
    {
        $path = ltrim($path, '/');
        $url = !empty($path) ? trailingslashit($this->baseUrl).$path : $this->baseUrl;
        return glsr(Sanitizer::class)->sanitizeUrl($url);
    }

    /**
     * Name the cached responses that flushAll() forgets together, whatever body they were asked with.
     */
    protected function flushGroup(string $path, string $transientKey): string
    {
        return sanitize_key($transientKey).'_'.Str::hash($this->url($path), 8);
    }

    /**
     * Return when flushAll() last ran for this transient key and path, or a blank.
     */
    protected function lastFlushed(string $path, string $transientKey): string
    {
        $lastFlushed = Arr::consolidate(get_site_option($this->lastFlushedKey()));
        return Cast::toString($lastFlushed[$this->flushGroup($path, $transientKey)] ?? '');
    }

    protected function lastFlushedKey(): string
    {
        return glsr()->prefix.'api_last_flushed';
    }

    protected function markReachable(): void
    {
        delete_site_transient($this->unreachableKey());
    }

    /**
     * Stop asking the host for a window that doubles with each failure from fifteen minutes to six hours.
     */
    protected function markUnreachable(string $url): void
    {
        $marker = Arr::consolidate(get_site_transient($this->unreachableKey()));
        $failures = max(0, Cast::toInt($marker['failures'] ?? 0)) + 1;
        $window = min(static::UNREACHABLE_INITIAL * 2 ** ($failures - 1), static::UNREACHABLE_MAX);
        $until = time() + $window;
        // The marker outlives its window so the failure count survives it.
        set_site_transient($this->unreachableKey(), compact('failures', 'until'), static::UNREACHABLE_MAX);
        glsr_log()->warning($this->unreachableMessage($url, $until));
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

    /**
     * Send the request with the connect timeout capped at CONNECT_TIMEOUT seconds.
     *
     * @return array|\WP_Error
     */
    protected function send(string $url, array $args)
    {
        // WP_Http passes only `timeout` to Requests so connect_timeout keeps its 10 second default.
        $connectTimeout = function (&$url, &$headers, &$data, &$type, &$options) use ($args) {
            $options['connect_timeout'] = max(1, min((int) $args['timeout'], static::CONNECT_TIMEOUT));
        };
        add_action('requests-requests.before_request', $connectTimeout, 10, 5);
        $result = wp_remote_request($url, $args);
        remove_action('requests-requests.before_request', $connectTimeout);
        return $result;
    }

    protected function timeUntilDeadline(): float
    {
        $now = microtime(true);
        return max($this->deadline - $now, 0.0);
    }

    protected function unreachableMessage(string $url, int $until): string
    {
        return sprintf('%s is unreachable, not asking again until %s.',
            wp_parse_url($url, \PHP_URL_HOST),
            wp_date('Y-m-d H:i:s', $until)
        );
    }

    protected function unreachableUntil(): int
    {
        $marker = Arr::consolidate(get_site_transient($this->unreachableKey()));
        return Cast::toInt($marker['until'] ?? 0);
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
