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

/**
 * Validates column consistency when inserting records into a CSV document.
 */
class ColumnConsistency
{
    /** @var int */
    protected $columns_count;

    /**
     * @param int $columns_count
     * @throws InvalidArgument if the column count is lesser than -1
     */
    public function __construct($columns_count = -1)
    {
        if ($columns_count < -1) {
            throw InvalidArgument::dueToInvalidColumnCount($columns_count, __METHOD__);
        }

        $this->columns_count = $columns_count;
    }

    /**
     * Returns the column count.
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columns_count;
    }

    /**
     * Tell whether the submitted record is valid.
     * @return bool
     */
    public function __invoke(array $record)
    {
        $count = count($record);
        if (-1 === $this->columns_count) {
            $this->columns_count = $count;

            return true;
        }

        return $count === $this->columns_count;
    }
}
