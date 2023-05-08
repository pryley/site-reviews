<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeDate extends StringSanitizer
{
    public function run(): string
    {
        $date = $this->value();
        $format = $this->args[0] ?? 'Y-m-d H:i:s';
        $formattedDate = \DateTime::createFromFormat($format, $date);
        if ($formattedDate && $date === $formattedDate->format($format)) {
            return $date;
        }
        $timestamp = strtotime($date);
        if (false === $timestamp) {
            return '';
        }
        $date = wp_date($format, $timestamp);
        if (false === $date) {
            return '';
        }
        return $date;
    }
}
