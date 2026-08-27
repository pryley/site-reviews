<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\League\Csv\TabularDataReader;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;
use GeminiLabs\SiteReviews\Defaults\ImportResultDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class ImportManager
{
    /**
     * The lock is re-armed by every import page request, so it lapses
     * only when a run dies between requests.
     */
    public const LOCK_EXPIRATION = 120;

    public const LOOKUP_CHUNK = 50;

    public function flush(): void
    {
        glsr(TableTmp::class)->drop();
    }

    public function import(int $limit = 1, int $offset = 0): array
    {
        $this->markImporting();
        $this->lock();
        set_time_limit(0);
        wp_raise_memory_limit('admin');
        wp_defer_term_counting(true);
        wp_suspend_cache_invalidation(true);
        $result = glsr(ImportResultDefaults::class)->defaults();
        $buffer = [];
        foreach ($this->records($limit, $offset) as $values) {
            $buffer[] = $values;
            if (static::LOOKUP_CHUNK === count($buffer)) {
                $this->importRecords($buffer, $result);
                $buffer = [];
            }
        }
        if (!empty($buffer)) {
            $this->importRecords($buffer, $result);
        }
        wp_defer_term_counting(false);
        wp_suspend_cache_invalidation(false);
        return glsr(ImportResultDefaults::class)->restrict($result);
    }

    public function importAttachments(int $limit = 1, int $offset = 0): array
    {
        $this->markImporting();
        $this->lock();
        set_time_limit(0);
        wp_raise_memory_limit('image');
        wp_suspend_cache_invalidation(true);
        $limit = max(1, $limit);
        $offset = max(0, $offset);
        $result = glsr()->filterArray('import/reviews/attachments', [], $limit, $offset);
        wp_suspend_cache_invalidation(false);
        return glsr(ImportResultDefaults::class)->restrict($result);
    }

    public function importedReview(string $submittedHash): ?Review
    {
        $reviewIds = $this->importedReviewIds([$submittedHash]);
        if (empty($reviewIds[$submittedHash])) {
            return null;
        }
        $review = glsr_get_review($reviewIds[$submittedHash]);
        if (!$review->isValid()) {
            return null;
        }
        return $review;
    }

    /**
     * Returns [submitted_hash => review_id] for the hashes that match an
     * imported review. The oldest review wins when several share a hash.
     */
    public function importedReviewIds(array $submittedHashes): array
    {
        if (empty($submittedHashes)) {
            return [];
        }
        $hashes = implode("','", array_map('esc_sql', $submittedHashes));
        $sql = "
            SELECT pm.meta_value AS submitted_hash, MIN(p.ID) AS review_id
            FROM table|posts AS p
            INNER JOIN table|postmeta AS pm ON (pm.post_id = p.ID)
            WHERE 1=1
            AND p.post_type = %s
            AND pm.meta_key = '_submitted_hash'
            AND pm.meta_value IN ('{$hashes}')
            GROUP BY pm.meta_value
        ";
        $sql = glsr(Query::class)->sql($sql, glsr()->post_type);
        $results = glsr(Database::class)->dbGetResults($sql, \ARRAY_A);
        return array_column(Arr::consolidate($results), 'review_id', 'submitted_hash');
    }

    public function lock(): void
    {
        set_transient(glsr()->prefix.'import_lock', time(), static::LOCK_EXPIRATION);
    }

    public function locked(): bool
    {
        return false !== get_transient(glsr()->prefix.'import_lock');
    }

    public function markImporting(): void
    {
        if (!defined('WP_IMPORTING')) {
            define('WP_IMPORTING', true);
        }
    }

    public function prepare(): void
    {
        glsr(TableTmp::class)->create(); // create a temporary table for importing
    }

    public function records(int $limit = 1, int $offset = 0): TabularDataReader
    {
        $this->prepare();
        $reader = Reader::from($this->tempFilePath());
        $reader->setHeaderOffset(0);
        return (new Statement())
            ->offset(max(0, $offset))
            ->limit(max(1, $limit))
            ->process($reader);
    }

    public function tempFilePath(): string
    {
        $uploads = wp_upload_dir();
        if (!file_exists($uploads['basedir'])) {
            $uploads = wp_upload_dir(null, true, true); // maybe the site has been moved, so refresh the cached uploads path
        }
        $path = trailingslashit($uploads['basedir']);
        $path = trailingslashit($path.glsr()->id);
        $path = trailingslashit($path.'temp');
        wp_mkdir_p($path);
        return $path.'import.csv';
    }

    public function unlinkTempFile(): bool
    {
        $path = $this->tempFilePath();
        if (!is_file($path)) {
            return false;
        }
        unlink($path); // delete the temporary import file
        return true;
    }

    public function unlock(): void
    {
        delete_transient(glsr()->prefix.'import_lock');
    }

    /**
     * skipped counts every record that is not imported. duplicates and failed
     * count the reasons inside it. A record that submits nothing is neither.
     */
    protected function importRecords(array $records, array &$result): void
    {
        $items = [];
        foreach ($records as $values) {
            $request = new Request($values);
            $command = new CreateReview($request);
            $submitted = $command->submitted();
            if (empty($submitted['submitted'])) {
                ++$result['skipped'];
                continue;
            }
            $items[] = [$request, $command, $submitted['submitted_hash']];
        }
        $reviewIds = $this->importedReviewIds(array_unique(array_column($items, 2)));
        foreach ($items as [$request, $command, $hash]) {
            $review = empty($reviewIds[$hash]) ? null : glsr_get_review($reviewIds[$hash]);
            if ($review?->isValid()) {
                $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, false);
                ++$result['duplicates'];
                ++$result['skipped'];
                continue;
            }
            if ($review = glsr(ReviewManager::class)->create($command)) {
                $reviewIds[$hash] = $review->ID; // a twin later in this chunk must match this review
                $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, true);
                ++$result['imported'];
                continue;
            }
            ++$result['failed'];
            ++$result['skipped'];
        }
    }
}
