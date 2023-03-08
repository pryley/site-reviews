<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeVersion extends StringSanitizer
{
    public function run(): string
    {
        $pattern = '/^(\d+\.)?(\d+\.)?(\d+)(-[a-z0-9]+)?$/i';
        if (1 === preg_match($pattern, $this->value(), $matches)) {
            return $matches[0];
        }
        return '';
    }
}
