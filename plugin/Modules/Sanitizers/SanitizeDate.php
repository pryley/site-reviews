<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Modules\Date;

class SanitizeDate extends StringSanitizer
{
    public function run(): string
    {
        $date = $this->value();
        $format = empty($this->args[0]) ? 'Y-m-d H:i:s' : $this->args[0];
        if (glsr(Date::class)->isDate($date, $format)) {
            return $date;
        }
        if (glsr(Date::class)->isTimestamp($date)) {
            return wp_date($format, (int) $date) ?: '';
        }
        $timestamp = strtotime($date);
        if (false === $timestamp) {
            return '';
        }
        return wp_date($format, $timestamp) ?: '';
    }
}
