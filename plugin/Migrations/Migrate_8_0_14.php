<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class Migrate_8_0_14 implements MigrateContract
{
    public function run(): bool
    {
        return $this->backfillMissingPostDateGmt();
    }

    /**
     * Backfill missing post_date_gmt values. This computes the site's UTC
     * offset periods (DST transitions) and runs one bulk UPDATE per period.
     */
    public function backfillMissingPostDateGmt(): bool
    {
        $range = $this->dateRange();
        if (2 !== count($range)) {
            return true; // nothing to migrate
        }
        $result = true;
        foreach ($this->offsetPeriods(...$range) as $period) {
            // The INTERVAL value must never be negative: while negative
            // values are valid in MySQL/MariaDB, both translators of the
            // SQLite integration plugin prepend the sign to the value,
            // producing an invalid double-signed SQLite modifier which
            // silently evaluates to NULL. Choosing DATE_ADD/DATE_SUB by
            // the offset's sign is compatible with all of them.
            $function = $period['offset'] < 0 ? 'DATE_ADD' : 'DATE_SUB';
            $sql = "
                UPDATE table|posts
                SET post_date_gmt = {$function}(post_date, INTERVAL %d SECOND)
                WHERE 1=1
                AND post_type = %s
                AND post_date_gmt = '0000-00-00 00:00:00'
                AND post_date >= %s
                AND post_date < %s
            ";
            $query = glsr(Query::class)->sql($sql,
                abs($period['offset']),
                glsr()->post_type,
                $period['from'],
                $period['to']
            );
            if (false === glsr(Database::class)->dbQuery($query)) {
                glsr_log()->error(
                    sprintf('The missing post_date_gmt of reviews between [%s] and [%s] could not be updated.',
                        $period['from'],
                        $period['to']
                    )
                );
                $result = false;
            }
        }
        return $result;
    }

    /**
     * The min/max post_date of the affected reviews.
     */
    protected function dateRange(): array
    {
        $sql = "
            SELECT MIN(post_date) AS min_date, MAX(post_date) AS max_date
            FROM table|posts
            WHERE 1=1
            AND post_type = %s
            AND post_date_gmt = '0000-00-00 00:00:00'
            AND post_date <> '0000-00-00 00:00:00'
        ";
        $query = glsr(Query::class)->sql($sql, glsr()->post_type);
        $row = glsr(Database::class)->dbGetRow($query, ARRAY_A);
        if (empty($row['min_date']) || empty($row['max_date'])) {
            return [];
        }
        return [$row['min_date'], $row['max_date']];
    }

    /**
     * The UTC offset periods (i.e. DST transitions) covering the given local
     * datetime range. This is typically 2 periods per year of reviews (or a
     * single period for timezones without DST).
     *
     * @return array<array{offset:int, from:string, to:string}>
     */
    protected function offsetPeriods(string $minDate, string $maxDate): array
    {
        $timezone = wp_timezone();
        $begin = (int) strtotime("{$minDate} UTC") - (2 * DAY_IN_SECONDS);
        $end = (int) strtotime("{$maxDate} UTC") + (2 * DAY_IN_SECONDS);
        $transitions = $timezone->getTransitions($begin, $end);
        if (empty($transitions)) { // fixed offset timezone
            $transitions = [[
                'offset' => $timezone->getOffset(new \DateTimeImmutable('now', $timezone)),
                'ts' => $begin,
            ]];
        }
        $periods = [];
        foreach ($transitions as $index => $transition) {
            $offset = (int) $transition['offset'];
            $from = ((int) $transition['ts']) + $offset;
            if (isset($transitions[$index + 1])) {
                $next = $transitions[$index + 1];
                // Use the larger of the two offsets at the boundary so that
                // local times inside a spring-forward gap (times skipped by
                // the clock) still fall into a period instead of leaving the
                // row unmigrated. This matches how PHP's DateTime normalizes
                // nonexistent local times.
                $to = ((int) $next['ts']) + max($offset, (int) $next['offset']);
            } else {
                $to = $end + $offset;
            }
            $periods[] = [
                'offset' => $offset,
                'from' => gmdate('Y-m-d H:i:s', $from),
                'to' => gmdate('Y-m-d H:i:s', $to),
            ];
        }
        return $periods;
    }
}
