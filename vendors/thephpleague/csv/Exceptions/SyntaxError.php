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
 * SyntaxError Exception.
 */
class SyntaxError extends UnableToProcessCsv
{
    /**
     * @var array<string>
     */
    protected $duplicateColumnNames = [];

    /**
     * DEPRECATION WARNING! This class will be removed in the next major point release.
     *
     * @deprecated since version 9.7.0
     * 
     * @param null|\Throwable $previous
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function dueToHeaderNotFound($offset)
    {
        return new self('The header record does not exist or is empty at offset: `'.$offset.'`');
    }

    public static function dueToInvalidHeaderColumnNames()
    {
        return new self('The header record contains non string column names.');
    }

    public static function dueToDuplicateHeaderColumnNames(array $header)
    {
        $instance = new self('The header record contains duplicate column names.');
        $instance->duplicateColumnNames = array_keys(array_filter(array_count_values($header), function ($value) {
            return $value > 1;
        }));

        return $instance;
    }

    public function duplicateColumnNames()
    {
        return $this->duplicateColumnNames;
    }
}
