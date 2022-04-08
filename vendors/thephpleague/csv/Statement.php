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

use ArrayIterator;
use CallbackFilterIterator;
use GeminiLabs\League\Csv\Exceptions\InvalidArgument;
use Iterator;
use LimitIterator;

/**
 * Criteria to filter a {@link Reader} object.
 */
class Statement
{
    /** @var array<callable> Callables to filter the iterator. */
    protected $where = [];
    /** @var array<callable> Callables to sort the iterator. */
    protected $order_by = [];
    /** iterator Offset. */
    protected $offset = 0;
    /** iterator maximum length. */
    protected $limit = -1;

    /**
     * @param callable $where
     * @param int $offset
     * @param int $limit
     * @return self
     * @throws UnableToProcessCsv
     */
    public static function create($where = null, $offset = 0, $limit = -1)
    {
        $stmt = new self();
        if (null !== $where) {
            $stmt = $stmt->where($where);
        }

        return $stmt->offset($offset)->limit($limit);
    }

    /**
     * Set the Iterator filter method.
     * @param callable $where
     * @return self
     */
    public function where($where)
    {
        $clone = clone $this;
        $clone->where[] = $where;

        return $clone;
    }

    /**
     * Set an Iterator sorting callable function.
     * @param callable $order_by
     * @return self
     */
    public function orderBy($order_by)
    {
        $clone = clone $this;
        $clone->order_by[] = $order_by;

        return $clone;
    }

    /**
     * Set LimitIterator Offset.
     *
     * @param int $offset
     * @return self
     * @throws UnableToProcessCsv if the offset is lesser than 0
     */
    public function offset($offset)
    {
        if (0 > $offset) {
            throw InvalidArgument::dueToInvalidRecordOffset($offset, __METHOD__);
        }

        if ($offset === $this->offset) {
            return $this;
        }

        $clone = clone $this;
        $clone->offset = $offset;

        return $clone;
    }

    /**
     * Set LimitIterator Count.
     *
     * @param int $limit
     * @return self
     * @throws UnableToProcessCsv if the limit is lesser than -1
     */
    public function limit($limit)
    {
        if (-1 > $limit) {
            throw InvalidArgument::dueToInvalidLimit($limit, __METHOD__);
        }

        if ($limit === $this->limit) {
            return $this;
        }

        $clone = clone $this;
        $clone->limit = $limit;

        return $clone;
    }

    /**
     * Execute the prepared Statement on the {@link Reader} object.
     *
     * @param array<string> $header an optional header to use instead of the CSV document header
     * @return TabularDataReader
     */
    public function process(TabularDataReader $tabular_data, array $header = [])
    {
        if ([] === $header) {
            $header = $tabular_data->getHeader();
        }

        $iterator = $tabular_data->getRecords($header);
        $iterator = array_reduce($this->where, [$this, 'filter'], $iterator);
        $iterator = $this->buildOrderBy($iterator);

        return new ResultSet(new LimitIterator($iterator, $this->offset, $this->limit), $header);
    }

    /**
     * Filters elements of an Iterator using a callback function.
     * @param callable $callable
     * @return CallbackFilterIterator
     */
    protected function filter(Iterator $iterator, $callable)
    {
        return new CallbackFilterIterator($iterator, $callable);
    }

    /**
     * Sort the Iterator.
     * @return Iterator
     */
    protected function buildOrderBy(Iterator $iterator)
    {
        if ([] === $this->order_by) {
            return $iterator;
        }

        $compare = function (array $record_a, array $record_b) {
            foreach ($this->order_by as $callable) {
                if (0 !== ($cmp = $callable($record_a, $record_b))) {
                    return $cmp;
                }
            }

            return isset($cmp) ? $cmp : 0;
        };

        $it = new ArrayIterator();
        foreach ($iterator as $offset => $value) {
            $it[$offset] = $value;
        }
        $it->uasort($compare);

        return $it;
    }
}
