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

use CallbackFilterIterator;
use GeminiLabs\League\Csv\Exceptions\InvalidArgument;
use GeminiLabs\League\Csv\Exceptions\SyntaxError;
use GeminiLabs\League\Csv\Polyfill\EmptyEscapeParser;
use Iterator;
use JsonSerializable;
use SplFileObject;

/**
 * A class to parse and read records from a CSV document.
 */
class Reader extends AbstractCsv implements TabularDataReader, JsonSerializable
{
    const STREAM_FILTER_MODE = STREAM_FILTER_READ;

    /** @var int|null */
    protected $header_offset = null;
    /** @var int */
    protected $nb_records = -1;
    /** @var bool */
    protected $is_empty_records_included = false;
    /** @var array<string> header record. */
    protected $header = [];

    /**
     * {@inheritdoc}
     */
    public static function createFromPath($path, $open_mode = 'r', $context = null)
    {
        return parent::createFromPath($path, $open_mode, $context);
    }

    /**
     * {@inheritdoc}
     */
    protected function resetProperties()
    {
        $this->nb_records = -1;
        $this->header = [];
    }

    /**
     * Returns the header offset.
     * @return int|null
     */
    public function getHeaderOffset()
    {
        return $this->header_offset;
    }

    /**
     * @return array
2     */
    public function getHeader()
    {
        if (null === $this->header_offset) {
            return $this->header;
        }

        if ([] !== $this->header) {
            return $this->header;
        }

        $this->header = $this->setHeader($this->header_offset);

        return $this->header;
    }

    /**
     * Determine the CSV record header.
     *
     * @throws UnableToProcessCsv If the header offset is set and no record is found or is the empty array
     *
     * @param int $offset
     * @return array<string>
     */
    protected function setHeader($offset)
    {
        $header = $this->seekRow($offset);
        if (in_array($header, [[], [null]], true)) {
            throw SyntaxError::dueToHeaderNotFound($offset);
        }

        if (0 !== $offset) {
            return $header;
        }

        $header = $this->removeBOM($header, mb_strlen($this->getInputBOM()), $this->enclosure);
        if ([''] === $header) {
            throw SyntaxError::dueToHeaderNotFound($offset);
        }

        return $header;
    }

    /**
     * Returns the row at a given offset.
     * @param int $offset
     * @return arrau
     */
    protected function seekRow($offset)
    {
        foreach ($this->getDocument() as $index => $record) {
            if ($offset === $index) {
                return $record;
            }
        }

        return [];
    }

    /**
     * Returns the document as an Iterator.
     * @return Iterator
     */
    protected function getDocument()
    {
        if (70400 > PHP_VERSION_ID && '' === $this->escape) {
            $this->document->setCsvControl($this->delimiter, $this->enclosure);

            return EmptyEscapeParser::parse($this->document);
        }

        $this->document->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD);
        $this->document->setCsvControl($this->delimiter, $this->enclosure, $this->escape);
        $this->document->rewind();

        return $this->document;
    }

    /**
     * Strip the BOM sequence from a record.
     *
     * @param string[] $record
     * @param int $bom_length
     * @param string $enclosure
     *
     * @return array<string>
     */
    protected function removeBOM(array $record, $bom_length, $enclosure)
    {
        if (0 === $bom_length) {
            return $record;
        }

        $record[0] = mb_substr($record[0], $bom_length);
        if ($enclosure.$enclosure != substr($record[0].$record[0], strlen($record[0]) - 1, 2)) {
            return $record;
        }

        $record[0] = substr($record[0], 1, -1);

        return $record;
    }

    /**
     * @param string $name
     * @return Iterator
     */
    public function fetchColumnByName($name)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchColumnByName($name);
    }

    /**
     * @param int $offset
     * @return Iterator
     */
    public function fetchColumnByOffset($offset = 0)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchColumnByOffset($offset);
    }

    /**
     * @param int $index
     * @return Iterator
     */
    public function fetchColumn($index = 0)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchColumn($index);
    }

    /**
     * @param int $nth_record
     * @return array
     */
    public function fetchOne($nth_record = 0)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchOne($nth_record);
    }

    /**
     * @param int $offset_index
     * @param int $value_index
     * @return Iterator
     */
    public function fetchPairs($offset_index = 0, $value_index = 1)
    {
        return ResultSet::createFromTabularDataReader($this)->fetchPairs($offset_index, $value_index);
    }

    /**
     * @return int
     */
    public function count()
    {
        if (-1 === $this->nb_records) {
            $this->nb_records = iterator_count($this->getRecords());
        }

        return $this->nb_records;
    }

    /**
     * @return Iterator
     */
    public function getIterator()
    {
        return $this->getRecords();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return iterator_to_array($this->getRecords(), false);
    }

    /**
     * @return Iterator
     */
    public function getRecords(array $header = [])
    {
        $header = $this->computeHeader($header);
        $normalized = function ($record) {
            return is_array($record) && ($this->is_empty_records_included || $record != [null]);
        };

        $bom = '';
        if (!$this->is_input_bom_included) {
            $bom = $this->getInputBOM();
        }

        $document = $this->getDocument();
        $records = $this->stripBOM(new CallbackFilterIterator($document, $normalized), $bom);
        if (null !== $this->header_offset) {
            $records = new CallbackFilterIterator($records, function (array $record, $offset) {
                return $offset !== $this->header_offset;
            });
        }

        if ($this->is_empty_records_included) {
            return $this->combineHeader(new MapIterator(
                $records,
                function (array $record) {
                    return ([null] === $record) ? [] : $record;
                }
            ), $header);
        }

        return $this->combineHeader($records, $header);
    }

    /**
     * Returns the header to be used for iteration.
     *
     * @param string[] $header
     *
     * @throws UnableToProcessCsv If the header contains non unique column name
     *
     * @return array<string>
     */
    protected function computeHeader(array $header)
    {
        if ([] === $header) {
            $header = $this->getHeader();
        }

        if ($header !== ($filtered_header = array_filter($header, 'is_string'))) {
            throw SyntaxError::dueToInvalidHeaderColumnNames();
        }

        if ($header !== array_unique($filtered_header)) {
            throw SyntaxError::dueToDuplicateHeaderColumnNames($header);
        }

        return $header;
    }

    /**
     * Combine the CSV header to each record if present.
     *
     * @param string[] $header
     * @return Iterator
     */
    protected function combineHeader(Iterator $iterator, array $header)
    {
        if ([] === $header) {
            return $iterator;
        }

        $field_count = count($header);
        $mapper = static function (array $record) use ($header, $field_count) {
            if (count($record) != $field_count) {
                $record = array_slice(array_pad($record, $field_count, null), 0, $field_count);
            }

            /** @var array<string|null> $assocRecord */
            $assocRecord = array_combine($header, $record);

            return $assocRecord;
        };

        return new MapIterator($iterator, $mapper);
    }

    /**
     * Strip the BOM sequence from the returned records if necessary.
     * @param string $bom
     * @return Iterator
     */
    protected function stripBOM(Iterator $iterator, $bom)
    {
        if ('' === $bom) {
            return $iterator;
        }

        $bom_length = mb_strlen($bom);
        $mapper = function (array $record, $index) use ($bom_length) {
            if (0 !== $index) {
                return $record;
            }

            $record = $this->removeBOM($record, $bom_length, $this->enclosure);
            if ([''] === $record) {
                return [null];
            }

            return $record;
        };

        return new CallbackFilterIterator(
            new MapIterator($iterator, $mapper),
            function (array $record) {
                return $this->is_empty_records_included || $record != [null];
            }
        );
    }

    /**
     * Selects the record to be used as the CSV header.
     *
     * Because the header is represented as an array, to be valid
     * a header MUST contain only unique string value.
     *
     * @param int|null $offset the header record offset
     *
     * @throws UnableToProcessCsv if the offset is a negative integer
     *
     * @return static
     */
    public function setHeaderOffset($offset)
    {
        if ($offset === $this->header_offset) {
            return $this;
        }

        if (null !== $offset && 0 > $offset) {
            throw InvalidArgument::dueToInvalidHeaderOffset($offset, __METHOD__);
        }

        $this->header_offset = $offset;
        $this->resetProperties();

        return $this;
    }

    /**
     * Enable skipping empty records.
     * @return self
     */
    public function skipEmptyRecords()
    {
        if ($this->is_empty_records_included) {
            $this->is_empty_records_included = false;
            $this->nb_records = -1;
        }

        return $this;
    }

    /**
     * Disable skipping empty records.
     * @return self
     */
    public function includeEmptyRecords()
    {
        if (!$this->is_empty_records_included) {
            $this->is_empty_records_included = true;
            $this->nb_records = -1;
        }

        return $this;
    }

    /**
     * Tells whether empty records are skipped by the instance.
     * @return bool
     */
    public function isEmptyRecordsIncluded()
    {
        return $this->is_empty_records_included;
    }
}
