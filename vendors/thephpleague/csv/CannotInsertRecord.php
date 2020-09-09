<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GeminiLabs\League\Csv;

/**
 * Thrown when a data is not added to the Csv Document.
 */
class CannotInsertRecord extends Exception
{
    /**
     * The record submitted for insertion.
     *
     * @var array
     */
    protected $record;

    /**
     * Validator which did not validated the data.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Create an Exception from a record insertion into a stream.
     * 
     * @return static
     */
    public static function triggerOnInsertion(array $record)
    {
        $exception = new self('Unable to write record to the CSV document');
        $exception->record = $record;

        return $exception;
    }

    /**
     * Create an Exception from a Record Validation.
     * 
     * @param string $name
     * 
     * @return static
     */
    public static function triggerOnValidation($name, array $record)
    {
        $exception = new self('Record validation failed');
        $exception->name = $name;
        $exception->record = $record;

        return $exception;
    }

    /**
     * return the validator name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * return the invalid data submitted.
     * 
     * @return array
     */
    public function getRecord()
    {
        return $this->record;
    }
}
