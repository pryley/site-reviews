<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeIpAddress extends StringSanitizer
{
    public function run(): string
    {
        $value = sanitize_text_field($this->value());
        if (false === filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            return '';
        }
        return $value;
    }
}
