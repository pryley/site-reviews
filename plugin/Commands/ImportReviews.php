<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\CharsetConverter;
use GeminiLabs\League\Csv\Exception;
use GeminiLabs\League\Csv\Info;
use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\League\Csv\TabularDataReader;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Upload;

class ImportReviews extends AbstractCommand
{
    use Upload;

    public const ALLOWED_DATE_FORMATS = [
        'd-m-Y', 'd-m-Y H:i', 'd-m-Y H:i:s',
        'd/m/Y', 'd/m/Y H:i', 'd/m/Y H:i:s',
        'm-d-Y', 'm-d-Y H:i', 'm-d-Y H:i:s',
        'm/d/Y', 'm/d/Y H:i', 'm/d/Y H:i:s',
        'Y-m-d', 'Y-m-d H:i', 'Y-m-d H:i:s',
        'Y/m/d', 'Y/m/d H:i', 'Y/m/d H:i:s',
    ];

    public const ALLOWED_DELIMITERS = [
        ',', ';',
    ];

    public const REQUIRED_KEYS = [
        'date', 'rating',
    ];

    protected string $dateFormat = 'Y-m-d';

    protected string $delimiter = '';

    /** @var string[] */
    protected array $errors = [];

    protected int $skippedRecords = 0;

    protected bool $success = false;

    public function __construct(Request $request)
    {
        $this->dateFormat = Str::restrictTo(static::ALLOWED_DATE_FORMATS, $request->date_format, 'Y-m-d');
        $this->delimiter = Str::restrictTo(static::ALLOWED_DELIMITERS, $request->delimiter, '');
        $this->errors = [];
    }

    public function handle(): void
    {
        if (!glsr()->hasPermission('tools', 'general')) {
            glsr(Notice::class)->addError(
                _x('You do not have permission to import reviews.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        if (!$this->validateUpload() || !$this->validateExtension('.csv')) {
            glsr(Notice::class)->addWarning(
                _x('The import file is not a valid CSV file.', 'admin-text', 'site-reviews')
            );
            $this->fail();
            return;
        }
        $result = $this->import();
        if (-1 === $result) {
            $this->fail();
            return;
        }
        $this->notify($result);
    }

    protected function createReader(): Reader
    {
        $reader = Reader::createFromPath($this->file()->tmp_name);
        if (empty($this->delimiter)) {
            $delimiters = Info::getDelimiterStats($reader, [',', ';']);
            $delimiters = array_keys(array_filter($delimiters));
            if (1 !== count($delimiters)) {
                throw new Exception(_x('Cannot detect the delimiter used in the CSV file (supported delimiters are comma and semicolon).', 'admin-text', 'site-reviews'));
            }
            $this->delimiter = $delimiters[0];
        }
        $reader->setDelimiter($this->delimiter);
        $reader->setHeaderOffset(0);
        $reader->skipEmptyRecords();
        if ($reader->supportsStreamFilterOnRead()) {
            $inputBom = $reader->getInputBOM();
            if (in_array($inputBom, [Reader::BOM_UTF16_LE, Reader::BOM_UTF16_BE], true)) {
                return CharsetConverter::addTo($reader, 'utf-16', 'utf-8'); // @phpstan-ignore-line
            } elseif (in_array($inputBom, [Reader::BOM_UTF32_LE, Reader::BOM_UTF32_BE], true)) {
                return CharsetConverter::addTo($reader, 'utf-32', 'utf-8'); // @phpstan-ignore-line
            }
        }
        return $reader;
    }

    protected function import(): int
    {
        define('WP_IMPORTING', true);
        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }
        try {
            wp_raise_memory_limit('admin');
            $reader = $this->createReader();
            $header = array_map('trim', $reader->getHeader());
            if (!empty(array_diff(static::REQUIRED_KEYS, $header))) {
                throw new Exception(_x('The CSV file could not be imported. Please verify the following details and try again:', 'admin-text', 'site-reviews'));
            }
            $records = Statement::create()
                ->where(fn (array $record) => !empty(array_filter($record, 'trim'))) // @phpstan-ignore-line remove empty rows
                ->where(fn (array $record) => $this->validateRecord($record))
                ->process($reader, $header);
            return $this->importRecords($records);
        } catch (Exception $e) {
            glsr(Notice::class)->addError($e->getMessage(), [
                '👉🏼 '._x('Does the CSV file include all required columns?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Have you named all of the columns in the CSV file?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Have you removed all empty columns from the CSV file?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Is the CSV file encoded as UTF-8?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Is the selected delimiter correct?', 'admin-text', 'site-reviews'),
            ]);
        } catch (\OutOfRangeException|\Exception $e) {
            glsr(Notice::class)->addError($e->getMessage());
        }
        return -1;
    }

    protected function importRecords(TabularDataReader $records): int
    {
        foreach ($records as $offset => $record) {
            $record = array_map('trim', $record);
            $date = \DateTime::createFromFormat($this->dateFormat, $record['date']);
            $record['date'] = $date->format('Y-m-d H:i:s'); // format the provided date
            $request = new Request($record);
            $command = new CreateReview($request);
            glsr(ReviewManager::class)->create($command);
        }
        glsr(Queue::class)->async('queue/recalculate-meta');
        return count($records);
    }

    protected function notify(int $result): void
    {
        $notice = sprintf(
            _nx('%s review was imported.', '%s reviews were imported.', $result, 'admin-text', 'site-reviews'),
            number_format_i18n($result)
        );
        if (0 === $this->skippedRecords) {
            glsr(Notice::class)->addSuccess($notice);
            return;
        }
        $skipped = sprintf(
            _nx('%s entry was skipped.', '%s entries were skipped.', $this->skippedRecords, 'admin-text', 'site-reviews'),
            number_format_i18n($this->skippedRecords)
        );
        $consoleLink = sprintf(_x('See the %s for more details.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">%s</a>',
                glsr_admin_url('tools', 'console'),
                _x('Console', 'admin-text', 'site-reviews')
            )
        );
        glsr(Notice::class)->addWarning(
            sprintf('%s (%s)', sprintf('%s %s', $notice, $skipped), $consoleLink)
        );
        glsr_log()->warning(
            sprintf('One or more of the following errors were encountered during import: %s', Str::naturalJoin($this->errors))
        );
    }

    protected function validateRecord(array $record): bool
    {
        $record = array_map('trim', $record);
        $required = [
            'date' => glsr(Date::class)->isDate(Arr::getAs('string', $record, 'date'), $this->dateFormat),
            'rating' => glsr(Rating::class)->isValid(Arr::getAs('int', $record, 'rating')),
        ];
        if (2 === count(array_filter($required))) {
            return true;
        }
        $errorMessages = [
            'date' => _x('wrong date format', 'admin-text', 'site-reviews'),
            'rating' => _x('empty or invalid rating', 'admin-text', 'site-reviews'),
        ];
        $errors = array_intersect_key($errorMessages, array_diff_key($required, array_filter($required)));
        $this->errors = array_merge($this->errors, $errors);
        ++$this->skippedRecords;
        return false;
    }
}
