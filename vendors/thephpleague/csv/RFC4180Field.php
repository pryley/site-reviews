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

use InvalidArgumentException;
use php_user_filter;

/**
 * A stream filter to conform the CSV field to RFC4180.
 *
 * DEPRECATION WARNING! This class will be removed in the next major point release
 *
 * @deprecated since version 9.2.0
 * @see AbstractCsv::setEscape
 *
 * @see https://tools.ietf.org/html/rfc4180#section-2
 */
class RFC4180Field extends php_user_filter
{
    const FILTERNAME = 'convert.league.csv.rfc4180';

    /**
     * The value being search for.
     *
     * @var string[]
     */
    protected $search;

    /**
     * The replacement value that replace found $search values.
     *
     * @var string[]
     */
    protected $replace;

    /**
     * Characters that triggers enclosure with PHP fputcsv.
     * @var string
     */
    protected static $force_enclosure = "\n\r\t ";

    /**
     * Static method to add the stream filter to a {@link AbstractCsv} object.
     * @param string $whitespace_replace
     * @return AbstractCsv
     */
    public static function addTo(AbstractCsv $csv, $whitespace_replace = '')
    {
        self::register();

        $params = [
            'enclosure' => $csv->getEnclosure(),
            'escape' => $csv->getEscape(),
            'mode' => $csv->getStreamFilterMode(),
        ];

        if ($csv instanceof Writer && '' != $whitespace_replace) {
            self::addFormatterTo($csv, $whitespace_replace);
            $params['whitespace_replace'] = $whitespace_replace;
        }

        return $csv->addStreamFilter(self::FILTERNAME, $params);
    }

    /**
     * Add a formatter to the {@link Writer} object to format the record
     * field to avoid enclosure around a field with an empty space.
     * @param string $whitespace_replace
     * @return Writer
     */
    public static function addFormatterTo(Writer $csv, $whitespace_replace)
    {
        if ('' == $whitespace_replace || strlen($whitespace_replace) != strcspn($whitespace_replace, self::$force_enclosure)) {
            throw new InvalidArgumentException('The sequence contains a character that enforces enclosure or is a CSV control character or is the empty string.');
        }

        $mapper = function ($value) use ($whitespace_replace) {
            return is_string($value)
                ? str_replace(' ', $whitespace_replace, $value)
                : $value;
        };

        return $csv->addFormatter(function (array $record) use ($mapper) {
            return array_map($mapper, $record);
        });
    }

    /**
     * Static method to register the class as a stream filter.
     */
    public static function register()
    {
        if (!in_array(self::FILTERNAME, stream_get_filters(), true)) {
            stream_filter_register(self::FILTERNAME, self::class);
        }
    }

    /**
     * Static method to return the stream filter filtername.
     * @return string
     */
    public static function getFiltername()
    {
        return self::FILTERNAME;
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
            $bucket->data = str_replace($this->search, $this->replace, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

    /**
     * @return bool
     */
    public function onCreate()
    {
        if (!$this->isValidParams($this->params)) {
            return false;
        }

        $this->search = [$this->params['escape'].$this->params['enclosure']];
        $this->replace = [$this->params['enclosure'].$this->params['enclosure']];
        if (STREAM_FILTER_WRITE != $this->params['mode']) {
            return true;
        }

        $this->search = [$this->params['escape'].$this->params['enclosure']];
        $this->replace = [$this->params['escape'].$this->params['enclosure'].$this->params['enclosure']];
        if ($this->isValidSequence($this->params)) {
            $this->search[] = $this->params['whitespace_replace'];
            $this->replace[] = ' ';
        }

        return true;
    }

    /**
     * Validate params property.
     * @return bool
     */
    protected function isValidParams(array $params)
    {
        static $mode_list = [STREAM_FILTER_READ => 1, STREAM_FILTER_WRITE => 1];

        return isset($params['enclosure'], $params['escape'], $params['mode'], $mode_list[$params['mode']])
            && 1 == strlen($params['enclosure'])
            && 1 == strlen($params['escape']);
    }

    /**
     * Is Valid White space replaced sequence.
     *
     * @return bool
     */
    protected function isValidSequence(array $params)
    {
        return isset($params['whitespace_replace'])
            && strlen($params['whitespace_replace']) == strcspn($params['whitespace_replace'], self::$force_enclosure);
    }
}
