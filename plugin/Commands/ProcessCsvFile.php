<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\League\Csv\CannotInsertRecord;
use GeminiLabs\League\Csv\CharsetConverter;
use GeminiLabs\League\Csv\Exception;
use GeminiLabs\League\Csv\Info;
use GeminiLabs\League\Csv\Reader;
use GeminiLabs\League\Csv\Statement;
use GeminiLabs\League\Csv\Writer;
use GeminiLabs\SiteReviews\Database\ImportManager;
use GeminiLabs\SiteReviews\Exceptions\FileNotFoundException;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Dump;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Upload;
use GeminiLabs\SiteReviews\UploadedFile;

class ProcessCsvFile extends AbstractCommand
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

    protected string $dateFormat;

    protected string $delimiter;

    protected array $errors;

    protected int $skipped;

    protected int $total;

    public function __construct(Request $request)
    {
        $this->dateFormat = Str::restrictTo(static::ALLOWED_DATE_FORMATS, $request->date_format, 'Y-m-d', true);
        $this->delimiter = Str::restrictTo(static::ALLOWED_DELIMITERS, $request->delimiter, '');
        $this->errors = [];
        $this->skipped = 0;
        $this->total = 0;
    }

    public function handle(): void
    {
        try {
            $file = $this->file();
        } catch (FileNotFoundException $e) {
            glsr(Notice::class)->addError($e->getMessage());
            $this->fail();
            return;
        }
        if (!$this->validateFile($file)) {
            $this->fail();
            return;
        }
        if (!$this->process($file)) {
            $this->fail();
        }
    }

    public function response(): array
    {
        return [
            'errors' => $this->errors, // @todo store this to the session
            'notices' => glsr(Notice::class)->get(), // this should be empty on success
            'skipped' => $this->skipped,
            'total' => $this->total,
        ];
    }

    protected function formatRecord(array $record): array
    {
        if (!empty($record['date'])) {
            $date = \DateTime::createFromFormat($this->dateFormat, $record['date']);
            $record['date'] = $date->format('Y-m-d H:i:s'); // format the provided date
        }
        return $record;
    }

    protected function process(UploadedFile $file): bool
    {
        if (!defined('WP_IMPORTING')) {
            define('WP_IMPORTING', true);
        }
        glsr(ImportManager::class)->flush(); // flush the temporary table in the database
        glsr(ImportManager::class)->unlinkTempFile(); // delete the temporary import file if it exists
        try {
            wp_raise_memory_limit('admin');
            $reader = $this->reader($file->getPathname());
            $header = array_map('trim', $reader->getHeader());
            if (!empty(array_diff(static::REQUIRED_KEYS, $header))) {
                throw new Exception(_x('The CSV file could not be imported. Please verify the following details and try again:', 'admin-text', 'site-reviews'));
            }
            $filePath = glsr(ImportManager::class)->tempFilePath();
            $writer = Writer::createFromPath($filePath, 'w+');
            $writer->insertOne($header);
            $writer->addFormatter(fn (array $record) => $this->formatRecord($record));
            $chunks = $reader->chunkBy(1000);
            foreach ($chunks as $chunk) {
                $records = Statement::create()
                    ->where(fn (array $record) => !empty(array_filter($record, 'trim'))) // @phpstan-ignore-line remove empty rows
                    ->where(fn (array $record) => $this->validateRecord($record))
                    ->process($reader, $header);
                $writer->insertAll($records);
                $this->total += count($records);
            }
            glsr(ImportManager::class)->prepare(); // create a temporary table for importing
            return true;
        } catch (CannotInsertRecord $e) {
            glsr(Notice::class)->addError(_x('Unable to process a row in the CSV document:', 'admin-text', 'site-reviews'), [
                glsr(Dump::class)->dump($e->getRecord()),
            ]);
        } catch (Exception $e) {
            glsr(Notice::class)->addError($e->getMessage(), [
                '👉🏼 '._x('Does the CSV file include all required columns?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Have you named all of the columns in the CSV file?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Have you removed all empty columns from the CSV file?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Have you selected the correct delimiter?', 'admin-text', 'site-reviews'),
                '👉🏼 '._x('Is the CSV file encoded as UTF-8?', 'admin-text', 'site-reviews'),
            ]);
        } catch (\OutOfRangeException|\Exception|\TypeError $e) {
            glsr(Notice::class)->addError($e->getMessage());
        }
        glsr(ImportManager::class)->unlinkTempFile();
        return false;
    }

    /**
     * @throws Exception
     */
    protected function reader(string $filepath): Reader
    {
        $reader = Reader::createFromPath($filepath);
        if (empty($this->delimiter)) {
            $delimiters = Info::getDelimiterStats($reader, static::ALLOWED_DELIMITERS);
            $delimiters = array_keys(array_filter($delimiters));
            if (1 !== count($delimiters)) {
                throw new Exception(_x('Cannot detect the delimiter used in the CSV file (supported delimiters are comma and semicolon).', 'admin-text', 'site-reviews'));
            }
            $this->delimiter = $delimiters[0];
        }
        $reader->setDelimiter($this->delimiter);
        $reader->setHeaderOffset(0);
        $reader->skipEmptyRecords();
        $reader->addFormatter(fn (array $record) => array_map('trim', $record));
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

    protected function validateFile(UploadedFile $file): bool
    {
        if (!$file->isValid()) {
            glsr(Notice::class)->addError($file->getErrorMessage());
            return false;
        }
        if (!$file->hasMimeType('text/csv')) {
            glsr(Notice::class)->addError(sprintf(
                _x('The import file does not look like a valid CSV file (detected: %s). If this is incorrect, make sure that your server is configured to detect mime types.', 'admin-text', 'site-reviews'),
                $file->getMimeType()
            ));
            return false;
        }
        return true;
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
            'date' => _x('Incorrect date format', 'admin-text', 'site-reviews'),
            'rating' => _x('Empty or invalid rating', 'admin-text', 'site-reviews'),
        ];
        $errors = array_intersect_key($errorMessages, array_diff_key($required, array_filter($required)));
        $this->errors = array_merge($this->errors, $errors);
        ++$this->skipped;
        return false;
    }
}
