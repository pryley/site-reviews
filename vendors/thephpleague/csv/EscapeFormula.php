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

/**
 * A Formatter to tackle CSV Formula Injection.
 *
 * @see http://georgemauer.net/2017/10/07/csv-injection.html
 */
class EscapeFormula
{
    /** Spreadsheet formula starting character. */
    const FORMULA_STARTING_CHARS = ['=', '-', '+', '@', "\t", "\r"];

    /** @var array Effective Spreadsheet formula starting characters. */
    protected $special_chars = [];
    /** @var string Escape character to escape each CSV formula field. */
    protected $escape;

    /**
     * @param string   $escape        escape character to escape each CSV formula field
     * @param string[] $special_chars additional spreadsheet formula starting characters
     */
    public function __construct($escape = "'", array $special_chars = [])
    {
        $this->escape = $escape;
        if ([] !== $special_chars) {
            $special_chars = $this->filterSpecialCharacters(...$special_chars);
        }

        $chars = array_unique(array_merge(self::FORMULA_STARTING_CHARS, $special_chars));
        $this->special_chars = array_fill_keys($chars, 1);
    }

    /**
     * Filter submitted special characters.
     *
     * @param string ...$characters
     *
     * @throws InvalidArgumentException if the string is not a single character
     *
     * @return array<string>
     */
    protected function filterSpecialCharacters(...$characters)
    {
        foreach ($characters as $str) {
            if (1 != strlen($str)) {
                throw new InvalidArgumentException('The submitted string '.$str.' must be a single character');
            }
        }

        return $characters;
    }

    /**
     * Returns the list of character the instance will escape.
     *
     * @return array<string>
     */
    public function getSpecialCharacters()
    {
        return array_keys($this->special_chars);
    }

    /**
     * Returns the escape character.
     * @return string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * League CSV formatter hook.
     *
     * @see escapeRecord
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        return $this->escapeRecord($record);
    }

    /**
     * Escape a CSV record.
     * @return array
     */
    public function escapeRecord(array $record)
    {
        return array_map([$this, 'escapeField'], $record);
    }

    /**
     * Escape a CSV cell if its content is stringable.
     *
     * @param int|float|string|object|resource|array $cell the content of the cell
     *
     * @return mixed the escaped content
     */
    protected function escapeField($cell)
    {
        if (!is_string($cell) && (!is_object($cell) || !method_exists($cell, '__toString'))) {
            return $cell;
        }

        $str_cell = (string) $cell;
        if (isset($str_cell[0], $this->special_chars[$str_cell[0]])) {
            return $this->escape.$str_cell;
        }

        return $cell;
    }

    /**
     * @deprecated since 9.7.2 will be removed in the next major release
     * @codeCoverageIgnore
     *
     * Tells whether the submitted value is stringable.
     *
     * @param mixed $value value to check if it is stringable
     * @return bool
     */
    protected function isStringable($value)
    {
        return is_string($value)
            || (is_object($value) && method_exists($value, '__toString'));
    }
}
