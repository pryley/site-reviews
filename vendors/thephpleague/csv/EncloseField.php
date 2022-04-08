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
 * A stream filter to improve enclosure character usage.
 *
 * @see https://tools.ietf.org/html/rfc4180#section-2
 * @see https://bugs.php.net/bug.php?id=38301
 */
class EncloseField extends php_user_filter
{
    const FILTERNAME = 'convert.league.csv.enclosure';

    /** @var string Default sequence. */
    protected $sequence;
    /** @var string Characters that triggers enclosure in PHP. */
    protected static $force_enclosure = "\n\r\t ";

    /**
     * Static method to return the stream filter filtername.
     * @return string
     */
    public static function getFiltername()
    {
        return self::FILTERNAME;
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
     * Static method to add the stream filter to a {@link Writer} object.
     *
     * @param string $sequence
     * @return Writer
     * @throws InvalidArgumentException if the sequence is malformed
     * @throws UnableToProcessCsv
     */
    public static function addTo(Writer $csv, $sequence)
    {
        self::register();

        if (!self::isValidSequence($sequence)) {
            throw new InvalidArgumentException('The sequence must contain at least one character to force enclosure');
        }

        return $csv
            ->addFormatter(function (array $record) use ($sequence) {
                return array_map(function ($value) use ($sequence) {
                    return $sequence.$value;
                }, $record);
            })
            ->addStreamFilter(self::FILTERNAME, ['sequence' => $sequence]);
    }

    /**
     * Filter type and sequence parameters.
     *
     * The sequence to force enclosure MUST contains one of the following character ("\n\r\t ")
     * @param string $sequence
     * @return bool
     */
    protected static function isValidSequence($sequence)
    {
        return strlen($sequence) != strcspn($sequence, self::$force_enclosure);
    }

    /**
     * @return bool
     */
    public function onCreate()
    {
        return isset($this->params['sequence'])
            && self::isValidSequence($this->params['sequence']);
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
            $bucket->data = str_replace($this->params['sequence'], '', $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
