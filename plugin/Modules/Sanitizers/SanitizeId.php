<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * This allows lowercase alphannumeric characters, dashes, and underscores.
 */
class SanitizeId extends StringSanitizer
{
    public const MAX_LENGTH = 32;

    public function run(): string
    {
        $value = sanitize_key($this->value());
        $value = preg_replace('/^(\d+)?(.*)/', '$2', $value);
        return substr($value, 0, static::MAX_LENGTH);
    }

    protected function value(): string
    {
        return (new SanitizeText($this->value))->run();
    }
}
