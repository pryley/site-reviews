<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Modules\Date;

class SanitizeTimestamp extends StringSanitizer
{
    public function run(): string
    {
        $timestamp = $this->value();
        if (glsr(Date::class)->isTimestamp($timestamp)) {
            return $timestamp;
        }
        return ''; // this is why it needs to be sanitized as a string!
    }
}
