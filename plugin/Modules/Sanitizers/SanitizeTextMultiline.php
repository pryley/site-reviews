<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        $value = html_entity_decode($this->value());
        $value = wp_strip_all_tags($value);
        $value = sanitize_textarea_field($value);
        return $this->kses($value);
    }
}
