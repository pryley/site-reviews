<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * This allows lowercase alphannumeric characters, underscores, and dashes.
 */
class SanitizeSlug extends StringSanitizer
{
    public function run(): string
    {
        return sanitize_title($this->value());
    }

    protected function value(): string
    {
        return (new SanitizeText($this->value))->run();
    }
}
