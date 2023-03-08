<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttrClass extends StringSanitizer
{
    public function run(): string
    {
        $classes = explode(' ', $this->value());
        foreach ($classes as $index => $value) {
            $value = sanitize_html_class($value);
            $value = preg_replace('/^(\d+)(.*)$/', '$2', $value);
            $classes[$index] = trim($value);
        }
        $classes = array_values(array_filter(array_unique($classes)));
        $classes = implode(' ', $classes);
        return $classes;
    }
}
