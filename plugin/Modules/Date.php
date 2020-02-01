<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SiteReviews\Helpers\Str;

class Date
{
    /**
     * [60, 1],
     * [60 * 100, 60],
     * [3600 * 70, 3600],
     * [3600 * 24 * 10, 3600 * 24],
     * [3600 * 24 * 30, 3600 * 24 * 7],
     * [3600 * 24 * 30 * 30, 3600 * 24 * 30],
     * [INF, 3600 * 24 * 265],.
     */
    protected static $TIME_PERIODS = [
        [60, 1],
        [6000, 60],
        [252000, 3600],
        [864000, 86400],
        [2592000, 604800],
        [77760000, 2592000],
        [INF, 22896000],
    ];

    /**
     * @param mixed $date
     * @param string $format
     * @return bool
     */
    public function isDate($date, $format = 'Y-m-d H:i:s')
    {
        $datetime = DateTime::createFromFormat($format, $date);
        return $datetime && $date == $datetime->format($format);
    }

    /**
     * @param mixed $date
     * @return bool
     */
    public function isTimestamp($date)
    {
        return ctype_digit($date)
            ? true
            : false;
    }

    /**
     * @param mixed $date
     * @param string $fallback
     * @return string
     */
    public function localized($date, $fallback = '')
    {
        return $this->isDate($date) || $this->isTimestamp($date)
            ? date_i18n('Y-m-d H:i', $date)
            : $fallback;
    }

    /**
     * @param mixed $date
     * @return string
     */
    public function relative($date)
    {
        if (!$this->isDate($date)) {
            return '';
        }
        $diff = time() - strtotime($date);
        foreach (static::$TIME_PERIODS as $i => $timePeriod) {
            if ($diff > $timePeriod[0]) {
                continue;
            }
            $unit = intval(floor($diff / $timePeriod[1]));
            $relativeDates = [
                _n('%s second ago', '%s seconds ago', $unit, 'site-reviews'),
                _n('%s minute ago', '%s minutes ago', $unit, 'site-reviews'),
                _n('an hour ago', '%s hours ago', $unit, 'site-reviews'),
                _n('yesterday', '%s days ago', $unit, 'site-reviews'),
                _n('a week ago', '%s weeks ago', $unit, 'site-reviews'),
                _n('%s month ago', '%s months ago', $unit, 'site-reviews'),
                _n('%s year ago', '%s years ago', $unit, 'site-reviews'),
            ];
            $relativeDate = $relativeDates[$i];
            return Str::contains($relativeDate, '%s')
                ? sprintf($relativeDate, $unit)
                : $relativeDate;
        }
    }
}
