<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeDate extends StringSanitizer
{
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public function run(): string
    {
        $date = $this->value();
        $formattedDate = \DateTime::createFromFormat(static::DATE_FORMAT, $date);
        if ($formattedDate && $date === $formattedDate->format(static::DATE_FORMAT)) {
            return $date;
        }
        $timestamp = strtotime($date);
        if (false === $timestamp) {
            return $this->args[0];
        }
        $date = wp_date(static::DATE_FORMAT, $timestamp);
        if (false === $date) {
            return $this->args[0];
        }
        return $date;
    }
}
