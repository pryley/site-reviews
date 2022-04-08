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

use const COUNT_RECURSIVE;

final class Info implements ByteSequence
{
    const BOM_SEQUENCE_LIST = [
        self::BOM_UTF32_BE,
        self::BOM_UTF32_LE,
        self::BOM_UTF16_BE,
        self::BOM_UTF16_LE,
        self::BOM_UTF8,
    ];

    /**
     * Returns the BOM sequence found at the start of the string.
     *
     * If no valid BOM sequence is found an empty string is returned
     * @param string $str
     * @return string
     */
    public static function fetchBOMSequence($str)
    {
        foreach (self::BOM_SEQUENCE_LIST as $sequence) {
            if (0 === strpos($str, $sequence)) {
                return $sequence;
            }
        }

        return '';
    }

    /**
     * Detect Delimiters usage in a {@link Reader} object.
     *
     * Returns a associative array where each key represents
     * a submitted delimiter and each value the number CSV fields found
     * when processing at most $limit CSV records with the given delimiter
     *
     * @param string[] $delimiters
     * @param int $limit
     *
     * @return array<string, int>
     */
    public static function getDelimiterStats(Reader $csv, array $delimiters, $limit = 1)
    {
        $delimiterFilter = static function ($value) {
            return 1 === strlen($value);
        };

        $recordFilter = static function ($record) {
            return 1 < count($record);
        };

        $stmt = Statement::create()->offset(0)->limit($limit);

        $delimiterStats = static function (array $stats, $delimiter) use ($csv, $stmt, $recordFilter) {
            $csv->setDelimiter($delimiter);
            $foundRecords = array_filter(
                iterator_to_array($stmt->process($csv)->getRecords(), false),
                $recordFilter
            );

            $stats[$delimiter] = count($foundRecords, COUNT_RECURSIVE);

            return $stats;
        };

        $currentDelimiter = $csv->getDelimiter();
        $currentHeaderOffset = $csv->getHeaderOffset();

        $csv->setHeaderOffset(null);

        $stats = array_reduce(
            array_unique(array_filter($delimiters, $delimiterFilter)),
            $delimiterStats,
            array_fill_keys($delimiters, 0)
        );

        $csv->setHeaderOffset($currentHeaderOffset);
        $csv->setDelimiter($currentDelimiter);

        return $stats;
    }
}
