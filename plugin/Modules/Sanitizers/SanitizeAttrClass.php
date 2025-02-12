<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Not using sanitize_html_class() because:
 * - allow ":" in class names
 * - ensure class name does not begin with a number
 */
class SanitizeAttrClass extends StringSanitizer
{
    public function run(): string
    {
        $classes = explode(' ', $this->value());
        foreach ($classes as $index => $value) {
            // Strip out any percent-encoded characters.
            $value = preg_replace('|%[a-fA-F0-9][a-fA-F0-9]|', '', $value);
            // Limit characters
            $value = preg_replace('/[^\:\w-]/', '', $value);
            // Ensure value does not begins with a number
            $value = preg_replace('/^(\d+)(.*)$/', '$2', $value);
            $classes[$index] = trim($value);
        }
        $classes = array_values(array_filter(array_unique($classes)));
        $classes = implode(' ', $classes);
        return $classes;
    }
}
