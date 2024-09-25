<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\League\Csv\TabularDataReader;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables\TableTmp;
use GeminiLabs\SiteReviews\Defaults\ImportResultDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class ImportManager
{
    public function flush(): void
    {
        glsr(TableTmp::class)->drop();
    }

    public function import(int $limit = 1, int $offset = 0): array
    {
        if (!defined('WP_IMPORTING')) {
            define('WP_IMPORTING', true);
        }
        set_time_limit(0);
        wp_raise_memory_limit('admin');
        wp_defer_term_counting(true);
        wp_suspend_cache_invalidation(true);
        $this->prepare(); // create a temporary table for importing
        $reader = Reader::createFromPath($this->tempFilePath());
        $reader->setHeaderOffset(0);
        $records = Statement::create()
            ->offset(max(0, $offset))
            ->limit(max(1, $limit))
            ->process($reader);
        $result = glsr(ImportResultDefaults::class)->defaults();
        foreach ($records as $values) {
            $request = new Request($values);
            $command = new CreateReview($request);
            if ($review = $this->importedReview($command->request)) {
                $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, false);
                $result['skipped']++;
                continue;
            }
            if ($review = glsr(ReviewManager::class)->create($command)) {
                $result['attachments'] += glsr()->filterInt('import/review/attachments', 0, $request, $review, true);
                $result['imported']++;
                continue;
            }
            $result['skipped']++;
        }
        wp_defer_term_counting(false);
        wp_suspend_cache_invalidation(false);
        unset($reader, $records);
        return glsr(ImportResultDefaults::class)->restrict($result);
    }

    public function importAttachments(int $limit = 1, int $offset = 0): array
    {
        if (!defined('WP_IMPORTING')) {
            define('WP_IMPORTING', true);
        }
        set_time_limit(0);
        wp_raise_memory_limit('image');
        wp_suspend_cache_invalidation(true);
        $limit = max(1, $limit);
        $offset = max(0, $offset);
        $result = glsr()->filterArray('import/reviews/attachments', [], $limit, $offset);
        wp_suspend_cache_invalidation(false);
        return glsr(ImportResultDefaults::class)->restrict($result);

    }

    public function importedReview(Request $request): ?Review
    {
        $submittedHash = md5(maybe_serialize($request->toArray()));
        $sql = "
            SELECT p.ID
            FROM table|posts AS p
            INNER JOIN table|postmeta AS pm ON (pm.post_id = p.ID)
            WHERE 1=1
            AND p.post_type = %s
            AND pm.meta_key = '_submitted_hash'
            AND pm.meta_value = %s
        ";
        $sql = glsr(Query::class)->sql($sql, glsr()->post_type, $submittedHash);
        $reviewId = glsr(Database::class)->dbGetVar($sql);
        $reviewId = Cast::toInt($reviewId);
        $review = glsr_get_review($reviewId);
        if (!$review->isValid()) {
            return null;
        }
        return $review;
    }

    public function prepare(): void
    {
        glsr(TableTmp::class)->create(); // create a temporary table for importing
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
}
