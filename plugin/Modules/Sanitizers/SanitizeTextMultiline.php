<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Strips all HTML from string while preserving new lines.
 */
class SanitizeTextMultiline extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->kses($this->value());
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_strip_all_tags($value);
        return sanitize_textarea_field($value);
    }
}
