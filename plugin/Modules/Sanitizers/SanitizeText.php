<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Strips all HTML from string.
 */
class SanitizeText extends StringSanitizer
{
    public function run(): string
    {
        return sanitize_text_field($this->value());
    }
}
