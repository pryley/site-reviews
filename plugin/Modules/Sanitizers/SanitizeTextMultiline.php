<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->value();
        $value = wp_kses(wp_unslash($value), []);
        return $this->kses($value);
    }
}
