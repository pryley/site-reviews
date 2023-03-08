<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * The regex pattern is used for a search/replace.
 */
class SanitizeRegex extends StringSanitizer
{
    public function run(): string
    {
        $pattern = $this->args[0];
        if (!empty($pattern)) {
            return preg_replace($pattern, '', $this->value());
        }
        return '';
    }
}
