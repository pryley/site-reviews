<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\Exception;
use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Upload;

class ImportReviews extends Upload implements Contract
{
    const REQUIRED_KEYS = [
        'content', 'date', 'rating',
    ];

    /**
     * @var int
     */
    protected $totalRecords = 0;

    /**
     * @return void
     */
    public function handle()
    {
        if (!$this->validateUpload()
            || !$this->validateExtension('.csv')) {
            return;
        }
        glsr()->store('import', true);
        $result = $this->import();
        glsr()->discard('import');
        if (false !== $result) {
            $this->notify($result);
        }
    }

    /**
     * @return int|bool
     */
    protected function import()
    {
        define('WP_IMPORTING', true);
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }
        require_once glsr()->path('vendors/thephpleague/csv/functions_include.php');
        try {
            wp_raise_memory_limit('admin');
            $reader = Reader::createFromPath($this->file()->tmp_name);
            $reader->setHeaderOffset(0);
            $header = array_map('trim', $reader->getHeader());
            if (!empty(array_diff(static::REQUIRED_KEYS, $header))) {
                throw new Exception('The CSV import header is missing required columns.');
            }
            $this->totalRecords = count($reader);
            $records = Statement::create()
                ->where(function ($record) {
                    return $this->validateRecord($record);
                })
                ->process($reader, $header);
            return $this->importRecords($records);
        } catch (Exception $e) {
            glsr(Notice::class)->addError($e->getMessage());
            return false;
        }
    }

    /**
     * @return int
     */
    protected function importRecords($records)
    {
        foreach ($records as $offset => $record) {
            $request = new Request($record);
            $command = new CreateReview($request);
            glsr(ReviewManager::class)->createRaw($command);
        }
        return count($records);
    }

    /**
     * @return void
     */
    protected function notify($result)
    {
        $skippedRecords = max(0, $this->totalRecords - $result);
        $notice = sprintf(
            _nx('%s review was imported.', '%s reviews were imported.', $result, 'admin-text', 'site-reviews'),
            number_format_i18n($result)
        );
        if (0 === $skippedRecords) {
            glsr(Notice::class)->addSuccess($notice);
            return;
        }
        $skipped = sprintf(
            _nx('%s entry was skipped.', '%s entries were skipped.', $skippedRecords, 'admin-text', 'site-reviews'),
            number_format_i18n($skippedRecords)
        );
        glsr(Notice::class)->addWarning(sprintf('%s %s', $notice, $skipped));
    }

    /**
     * @return bool
     */
    protected function validateRecord(array $record)
    {
        return !empty($record['content'])
            && glsr(Date::class)->isValid(Arr::get($record, 'date'), 'Y-m-d')
            && glsr(Rating::class)->isValid(Arr::get($record, 'rating'));
    }
}
