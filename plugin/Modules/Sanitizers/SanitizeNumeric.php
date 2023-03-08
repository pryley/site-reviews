<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeNumeric extends StringSanitizer
{
    public function run(): string
    {
        return is_numeric($this->value) ? $this->value() : '';
    }
}
