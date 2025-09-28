<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\StatDefaults;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Response;
use GeminiLabs\SiteReviews\Review;

class GeolocateReviews extends AbstractCommand
{
    /**
     * IP-API batch requests allow a maximum of 100 IPs per request.
     */
    public const BATCH_SIZE = 100;

    /**
     * Number of rows per insert query.
     */
    public const INSERT_CHUNK_SIZE = 500;

    /**
     * Transient key for processing lock.
     */
    public const LOCK_KEY = 'glsr_geolocation_processing_lock';

    /**
     * Key used for the queued action.
     */
    public const QUEUED_ACTION_KEY = 'queue/geolocations';

    /**
     * Integer number of rows to fetch per database query in generator.
     */
    public const ROW_BATCH_SIZE = 500;

    public function handle(): void
    {
        $this->queue(true);
    }

    /**
     * Process a batch of IPs for geolocation data.
     *
     * Fetches IPs, retrieves geolocation data, inserts stats, and updates post meta.
     *
     * @param int $offset Offset for IP query
     */
    public function process(int $offset = 0): void
    {
        $offset = max(0, $offset);
        $ipAddresses = $this->fetchIpsNeedingGeolocation($offset);
        if (empty($ipAddresses)) {
            return;
        }
        $response = $this->fetchRemoteGeolocationData($ipAddresses);
        $results = $response->body();
        if (empty($results)) {
            glsr_log()->warning("Geolocation: No geolocation data retrieved at offset {$offset}");
            return;
        }
        $validResults = $this->filterValidGeolocationResults($results);
        if (empty($validResults)) {
            glsr_log()->warning("Geolocation: No valid geolocation results at offset {$offset}");
            return;
        }
        $this->processResults($validResults);
        $this->scheduleNextBatchIfNeeded($offset, static::BATCH_SIZE, $ipAddresses);
    }

    public function processReview(Review $review): void
    {
        if (!$review->isValid()) {
            return;
        }
        if (Helper::isLocalIpAddress($review->ip_address)) {
            return;
        }
        $response = glsr(Geolocation::class)->lookup($review->ip_address);
        if ($response->failed()) {
            return;
        }
        $results = $this->filterValidGeolocationResults([$response->body()]);
        if (empty($results[0])) {
            return;
        }
        $result = glsr(StatDefaults::class)->restrict($results[0]);
        $result['rating_id'] = $review->rating_id;
        glsr(Database::class)->insert('stats', $result);
        $metadata = array_diff_key($result, ['rating_id' => 0]);
        glsr(PostMeta::class)->set($review->ID, 'geolocation', $metadata);
        glsr()->action('review/geolocated', $review, $result);
    }

    /**
     * Start processing via WP-Cron.
     */
    public function queue(bool $notify = false): bool
    {
        if (!glsr(Queue::class)->isPending(static::QUEUED_ACTION_KEY)) {
            $this->releaseLock();
        }
        if (get_transient(static::LOCK_KEY)) { // Prevent concurrent processing
            if ($notify) {
                glsr(Notice::class)->addWarning(
                    _x('Geolocation processing is already in progress.', 'admin-text', 'site-reviews')
                );
            }
            return false;
        }
        if (!$ipsToProcess = $this->countIpsNeedingGeolocation()) {
            if ($notify) {
                glsr(Notice::class)->addInfo(
                    _x('All valid IP addresses have already been geolocated.', 'admin-text', 'site-reviews')
                );
            }
            return false;
        }
        $this->lock();
        glsr(Queue::class)->once(time(), static::QUEUED_ACTION_KEY, ['offset' => 0], true);
        if ($notify) {
            glsr(Notice::class)->addSuccess(sprintf(
                _x('Successfully queued geolocation processing of %d IP addresses.', 'admin-text', 'site-reviews'),
                $ipsToProcess
            ));
        }
        return true;
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }

    protected function countIpsNeedingGeolocation(): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM table|ratings AS r
            LEFT JOIN table|stats AS s ON (r.ID = s.rating_id)
            WHERE 1=1
            AND r.ip_address IS NOT NULL
            AND r.ip_address != ''
            AND r.ip_address != '127.0.0.1'
            AND r.ip_address != 'unknown'
            AND s.rating_id IS NULL
        ";
        $query = glsr(Query::class)->sql($sql);
        return (int) glsr(Database::class)->dbGetVar($query);
    }

    /**
     * @param int $offset Offset for pagination
     */
    protected function fetchIpsNeedingGeolocation(int $offset): array
    {
        $sql = "
            SELECT DISTINCT r.ip_address
            FROM table|ratings AS r
            LEFT JOIN table|stats AS s ON (r.ID = s.rating_id)
            WHERE 1=1
            AND r.ip_address IS NOT NULL
            AND r.ip_address != ''
            AND r.ip_address != '127.0.0.1'
            AND r.ip_address != 'unknown'
            AND s.rating_id IS NULL
            LIMIT %d OFFSET %d
        ";
        $query = glsr(Query::class)->sql($sql, static::BATCH_SIZE, $offset);
        return glsr(Database::class)->dbGetCol($query);
    }

    /**
     * @param string[] $ipAddresses IPs to fetch data for
     */
    protected function fetchRemoteGeolocationData(array $ipAddresses): Response
    {
        $response = glsr(Geolocation::class)->batchLookup($ipAddresses);
        $remainingRequests = (int) $response->headers['x-rl'];
        $resetTime = max((int) $response->headers['x-ttl'], 60); // Min 60 seconds
        if (0 === $remainingRequests && $resetTime > 0) {
            glsr_log()->warning("Geolocation: Rate limit reached, waiting {$resetTime} seconds");
            sleep($resetTime);
        } else {
            if (422 === $response->code) {
                glsr_log()->error('Geolocation: 422 Unprocessable Entity, invalid batch request');
            }
            if (429 === $response->code) {
                glsr_log()->warning("Geolocation: 429 Too Many Requests, waiting {$resetTime} seconds");
                sleep($resetTime);
            }
        }
        return $response;
    }

    /**
     * @param array $results Geolocation API results
     */
    protected function filterValidGeolocationResults(array $results): array
    {
        return array_filter($results, function ($result) {
            $query = $result['query'] ?? '';
            $status = $result['status'] ?? '';
            return 'success' === $status && !empty($query);
        });
    }

    protected function lock(int $duration = \HOUR_IN_SECONDS): void
    {
        set_transient(static::LOCK_KEY, true, $duration);
    }

    /**
     * @param \Generator $generator Generator yielding ratings data
     * @param array      $results   Valid geolocation results
     */
    protected function prepareAndInsert(\Generator $generator, array $results): void
    {
        $data = [];
        $postmeta = [];
        $postmetaCol = [
            'post_id', 'meta_key', 'meta_value',
        ];
        $statsCol = array_keys(glsr(StatDefaults::class)->defaults());
        foreach ($generator as $item) {
            $result = current(array_filter($results, fn ($r) => $r['query'] === $item['ip_address']));
            $result = glsr(StatDefaults::class)->restrict(
                wp_parse_args($item, $result)
            );
            $data[] = $result;
            $postmeta[] = [
                'post_id' => $item['review_id'],
                'meta_key' => '_geolocation',
                'meta_value' => maybe_serialize(array_diff_key($result, ['rating_id' => 0])),
            ];
            if (count($data) >= static::INSERT_CHUNK_SIZE) {
                glsr(Database::class)->insertBulk('stats', $data, $statsCol);
                glsr(Database::class)->insertBulk('postmeta', $postmeta, $postmetaCol);
                $postmeta = [];
                $data = [];
            }
        }
        if (!empty($data) && !empty($postmeta)) {
            glsr(Database::class)->insertBulk('stats', $data, $statsCol);
            glsr(Database::class)->insertBulk('postmeta', $postmeta, $postmetaCol);
        }
    }

    protected function processResults(array $results): void
    {
        $validIps = wp_list_pluck($results, 'query');
        $generator = $this->resultsGenerator($validIps);
        $this->prepareAndInsert($generator, $results);
    }

    /**
     * Release the processing lock.
     */
    protected function releaseLock(): void
    {
        delete_transient(static::LOCK_KEY);
    }

    /**
     * Generator to yield ratings data for a list of IP addresses.
     * Uses pagination to handle large result sets efficiently.
     *
     * @param string[] $ipAddresses List of IPs to query
     */
    protected function resultsGenerator(array $ipAddresses): \Generator
    {
        $ipChunks = array_chunk($ipAddresses, static::BATCH_SIZE);
        foreach ($ipChunks as $chunk) {
            $offset = 0;
            $placeholders = implode(',', array_fill(0, count($chunk), '%s'));
            do {
                $sql = "
                    SELECT ip_address, ID AS rating_id, review_id
                    FROM table|ratings
                    WHERE ip_address IN ($placeholders)
                    LIMIT %d OFFSET %d
                ";
                $query = glsr(Query::class)->sql($sql, array_merge($chunk, [static::ROW_BATCH_SIZE, $offset]));
                $results = glsr(Database::class)->dbGetResults($query, \ARRAY_A);
                foreach ($results as $row) {
                    yield $row;
                }
                $offset += static::ROW_BATCH_SIZE;
                $hasResults = !empty($results);
                unset($results); // Free memory
            } while ($hasResults);
        }
    }

    /**
     * Schedule the next batch of IPs or release the lock if no more IPs remain.
     *
     * @param int   $offset      Current offset
     * @param int   $batchSize   Size of the current batch
     * @param array $ipAddresses Current batch of IPs
     */
    protected function scheduleNextBatchIfNeeded(int $offset, int $batchSize, array $ipAddresses, int $delay = 60): void
    {
        if (count($ipAddresses) === $batchSize) {
            $timestamp = time() + max(0, $delay);
            glsr(Queue::class)->once($timestamp, static::QUEUED_ACTION_KEY, ['offset' => $offset + $batchSize], true);
        } else {
            $this->releaseLock();
        }
    }
}
