<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeVersion extends StringSanitizer
{
    public function run(): string
    {
        $pattern = '/^(\d+\.){0,2}\d+(?:-[a-z]+(?:\.\d+|\d+)?)?$/i';
        if (1 === preg_match($pattern, $this->value(), $matches)) {
            return $matches[0];
        }
        return '';
    }
}
