<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Strips all HTML from string.
 */
class SanitizeText extends StringSanitizer
{
    public function run(): string
    {
        $value = html_entity_decode($this->value());
        $value = wp_strip_all_tags($value);
        return $this->kses(sanitize_text_field($value));
    }
}
