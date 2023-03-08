<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttr extends StringSanitizer
{
    public function run(): string
    {
        return esc_attr($this->value());
    }
}
