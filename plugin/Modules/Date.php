<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Date
{
    protected array $timePeriods;

    public function __construct()
    {
        $this->timePeriods = [
            [
                'future' => _nx_noop('in %s year', 'in %s years', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s year', '%s years', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s year ago', '%s years ago', '%s: amount of time', 'site-reviews'),
                'seconds' => YEAR_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s month', 'in %s months', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s month', '%s months', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s month ago', '%s months ago', '%s: amount of time', 'site-reviews'),
                'seconds' => MONTH_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s week', 'in %s weeks', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s week', '%s weeks', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s week ago', '%s weeks ago', '%s: amount of time', 'site-reviews'),
                'seconds' => WEEK_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s day', 'in %s days', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s day', '%s days', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s day ago', '%s days ago', '%s: amount of time', 'site-reviews'),
                'seconds' => DAY_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s hour', 'in %s hours', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s hour', '%s hours', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s hour ago', '%s hours ago', '%s: amount of time', 'site-reviews'),
                'seconds' => HOUR_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s minute', 'in %s minutes', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s minute', '%s minutes', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s minute ago', '%s minutes ago', '%s: amount of time', 'site-reviews'),
                'seconds' => MINUTE_IN_SECONDS,
            ],
            [
                'future' => _nx_noop('in %s second', 'in %s seconds', '%s: amount of time', 'site-reviews'),
                'name' => _nx_noop('%s second', '%s seconds', '%s: amount of time', 'site-reviews'),
                'past' => _nx_noop('%s second ago', '%s seconds ago', '%s: amount of time', 'site-reviews'),
                'seconds' => 1,
            ],
        ];
    }

    public function interval(int $seconds, string $tense = '', int $levels = 2): string
    {
        $tense = Str::restrictTo(['future', 'past'], $tense, '');
        if ($seconds <= 0) {
            return _nx('A moment ago', 'Now', (int) ('past' === $tense), 'The past and present/future tense of now', 'site-reviews');
        }
        $output = [];
        for (
            $index = 0, $level = 0;
            $index < count($this->timePeriods) && $seconds > 0 && $level < $levels;
            ++$index
        ) {
            $unit = intval(floor($seconds / $this->timePeriods[$index]['seconds']));
            if ($unit > 0) {
                ++$level;
                $seconds -= $unit * $this->timePeriods[$index]['seconds'];
                $output[] = $this->intervalLevel(compact('index', 'level', 'levels', 'seconds', 'tense', 'unit'));
            }
        }
        return implode(', ', $output);
    }

    /**
     * @param mixed $date
     */
    public function isDate($date, string $format = 'Y-m-d H:i:s'): bool
    {
        $datetime = \DateTime::createFromFormat($format, Cast::toString($date));
        return $datetime && $date === $datetime->format($format);
    }

    /**
     * @param mixed $date
     */
    public function isThisMonth($date): bool
    {
        return $this->isValid($date)
            ? date('Y', $this->toTimestamp($date)) === date('Y')
            : false;
    }

    /**
     * @param mixed $date
     */
    public function isThisYear($date): bool
    {
        return $this->isValid($date)
            ? date('m', $this->toTimestamp($date)) === date('m')
            : false;
    }

    /**
     * @param mixed $date
     */
    public function isTimestamp($date): bool
    {
        $date = Cast::toString($date);
        return ctype_digit($date)
            && 10 === strlen($date)
            && false !== strtotime($date);
    }

    /**
     * @param mixed $date
     */
    public function isValid($date, string $format = 'Y-m-d H:i:s'): bool
    {
        return $this->isDate($date, $format) || $this->isTimestamp($date);
    }

    /**
     * @param mixed $date
     */
    public function localized($date, string $fallback = ''): string
    {
        return $this->isValid($date)
            ? date_i18n('Y-m-d H:i:s', $date)
            : $fallback;
    }

    /**
     * @param mixed $date
     */
    public function relative($date): string
    {
        $seconds = time() - $this->toTimestamp($date);
        return $this->interval($seconds, 'past', 1);
    }

    /**
     * @param mixed $date
     */
    public function toTimestamp($date): int
    {
        if ($this->isTimestamp($date)) {
            return $date;
        }
        if ($this->isDate($date)) {
            return strtotime($date);
        }
        return time(); // fallback to the current time
    }

    protected function intervalLevel(array $args): string
    {
        $keys = ['index', 'level', 'levels', 'seconds', 'tense', 'unit'];
        $args = shortcode_atts(array_fill_keys($keys, 0), $args);
        extract($args);
        if (1 === $level && 'future' === $tense) { // @phpstan-ignore-line
            $string = $this->timePeriods[$index]['future'];
        } elseif (($level === $levels || $seconds <= 0) && 'past' === $tense) {
            $string = $this->timePeriods[$index]['past'];
        } else {
            $string = $this->timePeriods[$index]['name'];
        }
        return sprintf(translate_nooped_plural($string, $unit, 'site-reviews'), $unit);
    }
}
