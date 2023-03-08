<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        return sanitize_textarea_field($this->value());
    }
}
