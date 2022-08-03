<?php

/**
 * League.Csv (https://csv.thephpleague.com).
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv;

use OutOfRangeException;
use php_user_filter;
use Traversable;

/**
 * Converts resource stream or tabular data content charset.
 */
class CharsetConverter extends php_user_filter
{
    const FILTERNAME = 'convert.league.csv';

    /** @var string */
    protected $input_encoding = 'UTF-8';
    /** @var string */
    protected $output_encoding = 'UTF-8';

    /**
     * Static method to add the stream filter to a {@link AbstractCsv} object.
     * @param string $input_encoding
     * @param string $output_encoding
     * @return AbstractCsv
     */
    public static function addTo(AbstractCsv $csv, $input_encoding, $output_encoding)
    {
        self::register();

        return $csv->addStreamFilter(self::getFiltername($input_encoding, $output_encoding));
    }

    /**
     * Static method to register the class as a stream filter.
     * @return void
     */
    public static function register()
    {
        $filter_name = self::FILTERNAME.'.*';
        if (!in_array($filter_name, stream_get_filters(), true)) {
            stream_filter_register($filter_name, self::class);
        }
    }

    /**
     * Static method to return the stream filter filtername.
     * @param string $input_encoding
     * @param string $output_encoding
     * @return string
     */
    public static function getFiltername($input_encoding, $output_encoding)
    {
        return sprintf(
            '%s.%s/%s',
            self::FILTERNAME,
            self::filterEncoding($input_encoding),
            self::filterEncoding($output_encoding)
        );
    }

    /**
     * Filter encoding charset.
     *
     * @param string $encoding
     * @return string
     * @throws OutOfRangeException if the charset is malformed or unsupported
     */
    protected static function filterEncoding($encoding)
    {
        static $encoding_list;
        if (null === $encoding_list) {
            $list = mb_list_encodings();
            $encoding_list = array_combine(array_map('strtolower', $list), $list);
        }

        $key = strtolower($encoding);
        if (isset($encoding_list[$key])) {
            return $encoding_list[$key];
        }

        throw new OutOfRangeException('The submitted charset '.$encoding.' is not supported by the mbstring extension.');
    }

    /**
     * @return bool
     */
    public function onCreate()
    {
        $prefix = self::FILTERNAME.'.';
        if (0 !== strpos($this->filtername, $prefix)) {
            return false;
        }

        $encodings = substr($this->filtername, strlen($prefix));
        if (1 !== preg_match(',^(?<input>[-\w]+)\/(?<output>[-\w]+)$,', $encodings, $matches)) {
            return false;
        }

        try {
            $this->input_encoding = self::filterEncoding($matches['input']);
            $this->output_encoding = self::filterEncoding($matches['output']);
        } catch (OutOfRangeException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param resource $in
     * @param resource $out
     * @param int      $consumed
     * @param bool     $closing
     * @return int
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while (null !== ($bucket = stream_bucket_make_writeable($in))) {
            $bucket->data = @mb_convert_encoding($bucket->data, $this->output_encoding, $this->input_encoding);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

    /**
     * Convert Csv records collection into UTF-8.
     * @param iterable $records
     * @return iterable
     */
    public function convert($records)
    {
        if ($this->output_encoding === $this->input_encoding) {
            return $records;
        }

        if (is_array($records)) {
            return array_map($this, $records);
        }

        /* @var Traversable $records */
        return new MapIterator($records, $this);
    }

    /**
     * Enable using the class as a formatter for the {@link Writer}.
     * @return array
     */
    public function __invoke(array $record)
    {
        $outputRecord = [];
        foreach ($record as $offset => $value) {
            [$newOffset, $newValue] = $this->encodeField($value, $offset);
            $outputRecord[$newOffset] = $newValue;
        }

        return $outputRecord;
    }

    /**
     * Walker method to convert the offset and the value of a CSV record field.
     *
     * @param int|float|string|null $value  can be a scalar type or null
     * @param int|string            $offset can be a string or an int
     * @return array
     */
    protected function encodeField($value, $offset)
    {
        if (null !== $value && !is_numeric($value)) {
            $value = mb_convert_encoding($value, $this->output_encoding, $this->input_encoding);
        }

        if (!is_numeric($offset)) {
            $offset = mb_convert_encoding($offset, $this->output_encoding, $this->input_encoding);
        }

        return [$offset, $value];
    }

    /**
     * Sets the records input encoding charset.
     * @param string $encoding
     * @return self
     */
    public function inputEncoding($encoding)
    {
        $encoding = self::filterEncoding($encoding);
        if ($encoding === $this->input_encoding) {
            return $this;
        }

        $clone = clone $this;
        $clone->input_encoding = $encoding;

        return $clone;
    }

    /**
     * Sets the records output encoding charset.
     * @param string $encoding
     * @return self
     */
    public function outputEncoding($encoding)
    {
        $encoding = self::filterEncoding($encoding);
        if ($encoding === $this->output_encoding) {
            return $this;
        }

        $clone = clone $this;
        $clone->output_encoding = $encoding;

        return $clone;
    }
}
