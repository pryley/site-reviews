<?php

/**
 * League.Csv (https://csv.thephpleague.com).
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * @package league/csv v9.8.0
 */

namespace GeminiLabs\League\Csv;

use GeminiLabs\League\Csv\Exceptions\InvalidArgument;
use GeminiLabs\League\Csv\Exceptions\UnavailableFeature;
use GeminiLabs\League\Csv\Exceptions\UnavailableStream;
use Generator;
use SplFileObject;

/**
 * An abstract class to enable CSV document loading.
 */
abstract class AbstractCsv implements ByteSequence
{
    const STREAM_FILTER_MODE = STREAM_FILTER_READ;

    protected $document;
    protected $stream_filters = [];
    protected $input_bom = null;
    protected $output_bom = '';
    protected $delimiter = ',';
    protected $enclosure = '"';
    protected $escape = '\\';
    protected $is_input_bom_included = false;

    /**
     * @final This method should not be overwritten in child classes
     * @param SplFileObject|Stream $document The CSV Object instance
     */
    protected function __construct($document)
    {
        $this->document = $document;
        [$this->delimiter, $this->enclosure, $this->escape] = $this->document->getCsvControl();
        $this->resetProperties();
    }

    /**
     * Reset dynamic object properties to improve performance.
     *
     * @return void
     */
    abstract protected function resetProperties();

    public function __destruct()
    {
        unset($this->document);
    }

    public function __clone()
    {
        throw UnavailableStream::dueToForbiddenCloning(static::class);
    }

    /**
     * Return a new instance from a SplFileObject.
     *
     * @return static
     */
    public static function createFromFileObject(SplFileObject $file)
    {
        return new static($file);
    }

    /**
     * Return a new instance from a PHP resource stream.
     *
     * @param resource $stream
     * @return static
     */
    public static function createFromStream($stream)
    {
        return new static(new Stream($stream));
    }

    /**
     * Return a new instance from a string.
     *
     * @param string $content
     * @return static
     */
    public static function createFromString($content = '')
    {
        return new static(Stream::createFromString($content));
    }

    /**
     * Return a new instance from a file path.
     *
     * @param string $path
     * @param string $open_mode
     * @param resource|null $context the resource context
     * @return static
     */
    public static function createFromPath($path, $open_mode = 'r+', $context = null)
    {
        return new static(Stream::createFromPath($path, $open_mode, $context));
    }

    /**
     * Returns the current field delimiter.
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Returns the current field enclosure.
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Returns the pathname of the underlying document.
     * @return string
     */
    public function getPathname()
    {
        return $this->document->getPathname();
    }

    /**
     * Returns the current field escape character.
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * Returns the BOM sequence in use on Output methods.
     * @return string
     */
    public function getOutputBOM()
    {
        return $this->output_bom;
    }

    /**
     * Returns the BOM sequence of the given CSV.
     * @return string
     */
    public function getInputBOM()
    {
        if (null !== $this->input_bom) {
            return $this->input_bom;
        }

        $this->document->setFlags(SplFileObject::READ_CSV);
        $this->document->rewind();
        $this->input_bom = Info::fetchBOMSequence((string) $this->document->fread(4));
        if (is_null($this->input_bom)) {
            $this->input_bom = '';
        }

        return $this->input_bom;
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     * Returns the stream filter mode.
     *
     * @deprecated since version 9.7.0
     * @see AbstractCsv::supportsStreamFilterOnRead
     * @see AbstractCsv::supportsStreamFilterOnWrite
     * @return int
     */
    public function getStreamFilterMode()
    {
        return static::STREAM_FILTER_MODE;
    }

    /**
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     * Tells whether the stream filter capabilities can be used.
     *
     * @deprecated since version 9.7.0
     * @see AbstractCsv::supportsStreamFilterOnRead
     * @see AbstractCsv::supportsStreamFilterOnWrite
     * @return bool
     */
    public function supportsStreamFilter()
    {
        return $this->document instanceof Stream;
    }

    /**
     * Tells whether the stream filter read capabilities can be used.
     *
     * @return bool
     */
    public function supportsStreamFilterOnRead()
    {
        return $this->document instanceof Stream
            && (static::STREAM_FILTER_MODE & STREAM_FILTER_READ) === STREAM_FILTER_READ;
    }

    /**
     * Tells whether the stream filter write capabilities can be used.
     *
     * @return bool
     */
    public function supportsStreamFilterOnWrite()
    {
        return $this->document instanceof Stream
            && (static::STREAM_FILTER_MODE & STREAM_FILTER_WRITE) === STREAM_FILTER_WRITE;
    }

    /**
     * Tell whether the specify stream filter is attach to the current stream.
     *
     * @param string $filtername
     * @return bool
     */
    public function hasStreamFilter($filtername)
    {
        return $this->stream_filters[$filtername] ?? false;
    }

    /**
     * Tells whether the BOM can be stripped if presents.
     *
     * @return bool
     */
    public function isInputBOMIncluded()
    {
        return $this->is_input_bom_included;
    }

    /**
     * Returns the CSV document as a Generator of string chunk.
     *
     * @param int $length number of bytes read
     * @return Generator
     * @throws UnableToProcessCsv if the number of bytes is lesser than 1
     */
    public function chunk($length)
    {
        if ($length < 1) {
            throw InvalidArgument::dueToInvalidChunkSize($length, __METHOD__);
        }

        $input_bom = $this->getInputBOM();
        $this->document->rewind();
        $this->document->setFlags(0);
        $this->document->fseek(strlen($input_bom));
        /** @var array<int, string> $chunks */
        $chunks = str_split($this->output_bom.$this->document->fread($length), $length);
        foreach ($chunks as $chunk) {
            yield $chunk;
        }

        while ($this->document->valid()) {
            yield $this->document->fread($length);
        }
    }

    /**
     * Retrieves the CSV content
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated since version 9.1.0
     * @see AbstractCsv::toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Retrieves the CSV content.
     * DEPRECATION WARNING! This method will be removed in the next major point release.
     *
     * @deprecated since version 9.7.0
     * @see AbstractCsv::toString
     *
     * @return string
     */
    public function getContent()
    {
        return $this->toString();
    }

    /**
     * Retrieves the CSV content.
     *
     * @return string
     * @throws UnableToProcessCsv If the string representation can not be returned
     */
    public function toString()
    {
        $raw = '';
        foreach ($this->chunk(8192) as $chunk) {
            $raw .= $chunk;
        }

        return $raw;
    }

    /**
     * Outputs all data on the CSV file.
     *
     * @param string $filename
     * @return int returns the number of characters read from the handle
     *             and passed through to the output
     */
    public function output($filename = null)
    {
        if (null !== $filename) {
            $this->sendHeaders($filename);
        }

        $this->document->rewind();
        if (!$this->is_input_bom_included) {
            $this->document->fseek(strlen($this->getInputBOM()));
        }

        echo $this->output_bom;

        return strlen($this->output_bom) + (int) $this->document->fpassthru();
    }

    /**
     * Send the CSV headers.
     *
     * Adapted from Symfony\Component\HttpFoundation\ResponseHeaderBag::makeDisposition
     *
     * @param string $filename
     * @return void
     * @throws UnableToProcessCsv if the submitted header is invalid according to RFC 6266
     *
     * @see https://tools.ietf.org/html/rfc6266#section-4.3
     */
    protected function sendHeaders($filename)
    {
        if (strlen($filename) != strcspn($filename, '\\/')) {
            throw InvalidArgument::dueToInvalidHeaderFilename($filename);
        }

        $flag = FILTER_FLAG_STRIP_LOW;
        if (strlen($filename) !== mb_strlen($filename)) {
            $flag |= FILTER_FLAG_STRIP_HIGH;
        }

        /** @var string $filtered_name */
        $filtered_name = filter_var($filename, FILTER_UNSAFE_RAW, $flag);
        $filename_fallback = str_replace('%', '', $filtered_name);

        $disposition = sprintf('attachment; filename="%s"', str_replace('"', '\\"', $filename_fallback));
        if ($filename !== $filename_fallback) {
            $disposition .= sprintf("; filename*=utf-8''%s", rawurlencode($filename));
        }

        header('Content-Type: text/csv');
        header('Content-Transfer-Encoding: binary');
        header('Content-Description: File Transfer');
        header('Content-Disposition: '.$disposition);
    }

    /**
     * Sets the field delimiter.
     *
     * @throws InvalidArgument if the Csv control character is not one character only
     *
     * @param string $delimiter
     * @return static
     */
    public function setDelimiter($delimiter)
    {
        if ($delimiter === $this->delimiter) {
            return $this;
        }

        if (1 !== strlen($delimiter)) {
            throw InvalidArgument::dueToInvalidDelimiterCharacter($delimiter, __METHOD__);
        }

        $this->delimiter = $delimiter;
        $this->resetProperties();

        return $this;
    }

    /**
     * Sets the field enclosure.
     *
     * @throws InvalidArgument if the Csv control character is not one character only
     *
     * @param string $enclosure
     * @return static
     */
    public function setEnclosure($enclosure)
    {
        if ($enclosure === $this->enclosure) {
            return $this;
        }

        if (1 !== strlen($enclosure)) {
            throw InvalidArgument::dueToInvalidEnclosureCharacter($enclosure, __METHOD__);
        }

        $this->enclosure = $enclosure;
        $this->resetProperties();

        return $this;
    }

    /**
     * Sets the field escape character.
     *
     * @throws InvalidArgument if the Csv control character is not one character only
     *
     * @param string $escape
     * @return static
     */
    public function setEscape($escape)
    {
        if ($escape === $this->escape) {
            return $this;
        }

        if ('' !== $escape && 1 !== strlen($escape)) {
            throw InvalidArgument::dueToInvalidEscapeCharacter($escape, __METHOD__);
        }

        $this->escape = $escape;
        $this->resetProperties();

        return $this;
    }

    /**
     * Enables BOM Stripping.
     *
     * @return static
     */
    public function skipInputBOM()
    {
        $this->is_input_bom_included = false;

        return $this;
    }

    /**
     * Disables skipping Input BOM.
     *
     * @return static
     */
    public function includeInputBOM()
    {
        $this->is_input_bom_included = true;

        return $this;
    }

    /**
     * Sets the BOM sequence to prepend the CSV on output.
     *
     * @param string $str
     * @return static
     */
    public function setOutputBOM($str)
    {
        $this->output_bom = $str;

        return $this;
    }

    /**
     * append a stream filter.
     *
     * @param string $filtername
     * @param array|null $params
     *
     * @throws InvalidArgument    If the stream filter API can not be appended
     * @throws UnavailableFeature If the stream filter API can not be used
     *
     * @return static
     */
    public function addStreamFilter($filtername, $params = null)
    {
        if (!$this->document instanceof Stream) {
            throw UnavailableFeature::dueToUnsupportedStreamFilterApi(get_class($this->document));
        }

        $this->document->appendFilter($filtername, static::STREAM_FILTER_MODE, $params);
        $this->stream_filters[$filtername] = true;
        $this->resetProperties();
        $this->input_bom = null;

        return $this;
    }
}
