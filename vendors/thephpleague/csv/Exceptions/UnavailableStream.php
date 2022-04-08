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

final class UnavailableStream extends UnableToProcessCsv
{
    private function __construct($message)
    {
        parent::__construct($message);
    }

    public static function dueToPathNotFound($path)
    {
        return new self('`'.$path.'`: failed to open stream: No such file or directory.');
    }

    public static function dueToForbiddenCloning($class_name)
    {
        return new self('An object of class '.$class_name.' cannot be cloned.');
    }
}
