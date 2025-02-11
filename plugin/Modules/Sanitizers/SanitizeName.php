<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * This allows lowercase alpha characters, dashes, underscores, and brackets.
 */
class SanitizeName extends StringSanitizer
{
    public function run(): string
    {
        $value = strtolower($this->value());
        $value = preg_replace('/[^a-z_\-\[\]]/', '', $value);
        // $value = preg_replace('/[^a-z_]/', '', $value);
        if (empty(trim($value, '_-[]'))) {
            return '';
        }
        return $value;
    }

    protected function value(): string
    {
        return (new SanitizeText($this->value))->run();
    }
}
