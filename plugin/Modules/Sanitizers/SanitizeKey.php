<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This allows lowercase alphannumeric characters and underscores.
 */
class SanitizeKey extends StringSanitizer
{
    public const MAX_LENGTH = 32;

    public function run(): string
    {
        $value = sanitize_key($this->value());
        $value = substr(Str::snakeCase($value), 0, static::MAX_LENGTH);
        return esc_attr($value);
    }

    protected function value(): string
    {
        return (new SanitizeText($this->value))->run();
    }
}
