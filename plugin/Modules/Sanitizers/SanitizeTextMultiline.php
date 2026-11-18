<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Strips all HTML from string while preserving new lines.
 */
class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->kses($this->decode($this->value()));
        $value = wp_strip_all_tags($value);
        return sanitize_textarea_field($value);
    }
}
