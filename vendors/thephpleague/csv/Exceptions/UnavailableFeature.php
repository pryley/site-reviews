<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv\Exceptions;

/**
 * StreamFilterSupportMissing Exception.
 */
class UnavailableFeature extends UnableToProcessCsv
{
    /**
     * DEPRECATION WARNING! This class will be removed in the next major point release.
     *
     * @param null|\Throwable $previous
     * @deprecated since version 9.7.0
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function dueToUnsupportedStreamFilterApi($className)
    {
        return new self('The stream filter API can not be used with a '.$className.' instance.');
    }

    public static function dueToMissingStreamSeekability()
    {
        return new self('stream does not support seeking');
    }
}
