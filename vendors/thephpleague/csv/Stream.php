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

use GeminiLabs\League\Csv\Exceptions\InvalidArgument;
use GeminiLabs\League\Csv\Exceptions\UnavailableFeature;
use GeminiLabs\League\Csv\Exceptions\UnavailableStream;
use SeekableIterator;
use SplFileObject;
use TypeError;

/**
 * An object oriented API to handle a PHP stream resource.
 *
 * @internal used internally to iterate over a stream resource
 */
final class Stream implements SeekableIterator
{
    /** @var array<string, array<resource>> Attached filters. */
    private $filters = [];
    /** @var resource */
    private $stream;
    /** @var bool */
    private $should_close_stream = false;
    /** @var mixed can be a null false or a scalar type value. Current iterator value. */
    private $value;
    /** @var int Current iterator key. */
    private $offset;
    /** @var int Flags for the Document. */
    private $flags = 0;
    /** @var string */
    private $delimiter = ',';
    /** @var string */
    private $enclosure = '"';
    /** @var string */
    private $escape = '\\';
    /** @var bool */
    private $is_seekable = false;

    /**
     * @param mixed $stream stream type resource
     */
    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new TypeError('Argument passed must be a stream resource, '.gettype($stream).' given.');
        }

        if ('stream' !== ($type = get_resource_type($stream))) {
            throw new TypeError('Argument passed must be a stream resource, '.$type.' resource given');
        }

        $this->is_seekable = stream_get_meta_data($stream)['seekable'];
        $this->stream = $stream;
    }

    public function __destruct()
    {
        array_walk_recursive($this->filters, function ($filter) {
            return @stream_filter_remove($filter);
        });

        if ($this->should_close_stream && is_resource($this->stream)) {
            fclose($this->stream);
        }

        unset($this->stream);
    }

    public function __clone()
    {
        throw UnavailableStream::dueToForbiddenCloning(self::class);
    }

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return stream_get_meta_data($this->stream) + [
            'delimiter' => $this->delimiter,
            'enclosure' => $this->enclosure,
            'escape' => $this->escape,
            'stream_filters' => array_keys($this->filters),
        ];
    }

    /**
     * Return a new instance from a file path.
     *
     * @param string $path
     * @param string $open_mode
     * @param resource|null $context
     * @return self
     * @throws UnableToProcessCsv if the stream resource can not be created
     */
    public static function createFromPath($path, $open_mode = 'r', $context = null)
    {
        $args = [$path, $open_mode];
        if (null !== $context) {
            $args[] = false;
            $args[] = $context;
        }

        $resource = @fopen(...$args);
        if (!is_resource($resource)) {
            throw UnavailableStream::dueToPathNotFound($path);
        }

        $instance = new self($resource);
        $instance->should_close_stream = true;

        return $instance;
    }

    /**
     * Return a new instance from a string.
     * @param string $content
     * @return self
     */
    public static function createFromString($content = '')
    {
        /** @var resource $resource */
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, $content);

        $instance = new self($resource);
        $instance->should_close_stream = true;

        return $instance;
    }

    /**
     * returns the URI of the underlying stream.
     *
     * @return string
     *
     * @see https://www.php.net/manual/en/splfileinfo.getpathname.php
     */
    public function getPathname()
    {
        return stream_get_meta_data($this->stream)['uri'];
    }

    /**
     * append a filter.
     *
     * @see http://php.net/manual/en/function.stream-filter-append.php
     *
     * @param string $filtername
     * @param int $read_write
     *
     * @throws InvalidArgument if the filter can not be appended
     */
    public function appendFilter($filtername, $read_write, array $params = null)
    {
        $params = $params ?? [];
        $res = @stream_filter_append($this->stream, $filtername, $read_write, $params);
        if (!is_resource($res)) {
            throw InvalidArgument::dueToStreamFilterNotFound($filtername);
        }

        $this->filters[$filtername][] = $res;
    }

    /**
     * Set CSV control.
     *
     * @see http://php.net/manual/en/SplFileObject.setcsvcontrol.php
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function setCsvControl($delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        [$this->delimiter, $this->enclosure, $this->escape] = $this->filterControl($delimiter, $enclosure, $escape, __METHOD__);
    }

    /**
     * Filter Csv control characters.
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $caller
     * @return array
     *
     * @throws InvalidArgument if the Csv control character is not one character only
     */
    private function filterControl($delimiter, $enclosure, $escape, $caller)
    {
        if (1 !== strlen($delimiter)) {
            throw InvalidArgument::dueToInvalidDelimiterCharacter($delimiter, $caller);
        }

        if (1 !== strlen($enclosure)) {
            throw InvalidArgument::dueToInvalidEnclosureCharacter($enclosure, $caller);
        }

        if (1 === strlen($escape) || ('' === $escape && 70400 <= PHP_VERSION_ID)) {
            return [$delimiter, $enclosure, $escape];
        }

        throw InvalidArgument::dueToInvalidEscapeCharacter($escape, $caller);
    }

    /**
     * Set CSV control.
     *
     * @see http://php.net/manual/en/SplFileObject.getcsvcontrol.php
     *
     * @return array<string>
     */
    public function getCsvControl()
    {
        return [$this->delimiter, $this->enclosure, $this->escape];
    }

    /**
     * Set CSV stream flags.
     *
     * @param int $flags
     *
     * @see http://php.net/manual/en/SplFileObject.setflags.php
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Write a field array as a CSV line.
     *
     * @see http://php.net/manual/en/SplFileObject.fputcsv.php
     *
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param string $eol
     * @return int|false
     */
    public function fputcsv(array $fields, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = "\n")
    {
        $controls = $this->filterControl($delimiter, $enclosure, $escape, __METHOD__);
        if (80100 <= PHP_VERSION_ID) {
            $controls[] = $eol;
        }

        return fputcsv($this->stream, $fields, ...$controls);
    }

    /**
     * Get line number.
     *
     * @return int
     *
     * @see http://php.net/manual/en/SplFileObject.key.php
     */
    public function key()
    {
        return $this->offset;
    }

    /**
     * Read next line.
     *
     * @see http://php.net/manual/en/SplFileObject.next.php
     */
    public function next()
    {
        $this->value = false;
        ++$this->offset;
    }

    /**
     * Rewind the file to the first line.
     *
     * @see http://php.net/manual/en/SplFileObject.rewind.php
     *
     * @throws UnableToProcessCsv if the stream resource is not seekable
     */
    public function rewind()
    {
        if (!$this->is_seekable) {
            throw UnavailableFeature::dueToMissingStreamSeekability();
        }

        rewind($this->stream);
        $this->offset = 0;
        $this->value = false;
        if (0 !== ($this->flags & SplFileObject::READ_AHEAD)) {
            $this->current();
        }
    }

    /**
     * Not at EOF.
     *
     * @return bool
     *
     * @see http://php.net/manual/en/SplFileObject.valid.php
     */
    public function valid()
    {
        if (0 !== ($this->flags & SplFileObject::READ_AHEAD)) {
            return false !== $this->current();
        }

        return !feof($this->stream);
    }

    /**
     * Retrieves the current line of the file.
     *
     * @see http://php.net/manual/en/SplFileObject.current.php
     *
     * @return mixed the value of the current element
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        if (false !== $this->value) {
            return $this->value;
        }

        $this->value = $this->getCurrentRecord();

        return $this->value;
    }

    /**
     * Retrieves the current line as a CSV Record.
     *
     * @return array|false
     */
    private function getCurrentRecord()
    {
        $flag = 0 !== ($this->flags & SplFileObject::SKIP_EMPTY);
        do {
            $ret = fgetcsv($this->stream, 0, $this->delimiter, $this->enclosure, $this->escape);
        } while ($flag && is_array($ret) && null === $ret[0]);

        return $ret;
    }

    /**
     * Seek to specified line.
     *
     * @see http://php.net/manual/en/SplFileObject.seek.php
     *
     * @param  int       $position
     * @throws UnableToProcessCsv if the position is negative
     */
    public function seek($position)
    {
        if ($position < 0) {
            throw InvalidArgument::dueToInvalidSeekingPosition($position, __METHOD__);
        }

        $this->rewind();
        while ($this->key() !== $position && $this->valid()) {
            $this->current();
            $this->next();
        }

        if (0 !== $position) {
            --$this->offset;
        }

        $this->current();
    }

    /**
     * Output all remaining data on a file pointer.
     *
     * @see http://php.net/manual/en/SplFileObject.fpatssthru.php
     *
     * @return int|false
     */
    public function fpassthru()
    {
        return fpassthru($this->stream);
    }

    /**
     * Read from file.
     *
     * @see http://php.net/manual/en/SplFileObject.fread.php
     *
     * @param int<0, max> $length The number of bytes to read
     *
     * @return string|false
     */
    public function fread($length)
    {
        return fread($this->stream, $length);
    }

    /**
     * Gets a line from file.
     *
     * @see http://php.net/manual/en/SplFileObject.fgets.php
     *
     * @return string|false
     */
    public function fgets()
    {
        return fgets($this->stream);
    }

    /**
     * Seek to a position.
     *
     * @see http://php.net/manual/en/SplFileObject.fseek.php
     *
     * @param int $offset
     * @param int $whence
     * @return int
     * @throws UnableToProcessCsv if the stream resource is not seekable
     */
    public function fseek($offset, $whence = SEEK_SET)
    {
        if (!$this->is_seekable) {
            throw UnavailableFeature::dueToMissingStreamSeekability();
        }

        return fseek($this->stream, $offset, $whence);
    }

    /**
     * Write to stream.
     *
     * @see http://php.net/manual/en/SplFileObject.fwrite.php
     *
     * @param string $str
     * @param int $length
     * @return int|false
     */
    public function fwrite($str, $length = null)
    {
        $args = [$this->stream, $str];
        if (null !== $length) {
            $args[] = $length;
        }

        return fwrite(...$args);
    }

    /**
     * Flushes the output to a file.
     *
     * @return bool
     *
     * @see http://php.net/manual/en/SplFileObject.fwrite.php
     */
    public function fflush()
    {
        return fflush($this->stream);
    }
}
