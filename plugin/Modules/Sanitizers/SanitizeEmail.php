<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeEmail extends StringSanitizer
{
    public function run(): string
    {
        return sanitize_email($this->value());
    }
}
