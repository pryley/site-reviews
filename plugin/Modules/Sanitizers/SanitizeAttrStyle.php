<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;

class SanitizeAttrStyle extends StringSanitizer
{
    public function run(): string
    {
        $value = strtolower($this->value());
        $parts = explode(';', $value);
        $parts = array_map(fn ($propVal) => $this->sanitizePropertyValue($propVal), $parts);
        $parts = array_filter($parts);
        $value = implode(' ', $parts);
        $value = trim($value);
        return esc_attr($value);
    }

    protected function sanitizePropertyValue(string $propVal): string
    {
        if (str_starts_with($propVal, 'http')) {
            return '';
        }
        $parts = explode(':', $propVal, 2);
        $property = preg_replace('/[^a-z\-]/', '', $parts[0] ?? '');
        $value = preg_replace('/[^\w,.:!#%"()\-\'\/ ]/', '', $parts[1] ?? '');
        $value = trim($value);
        if (empty($property) || Helper::isEmpty($value)) {
            return '';
        }
        return "{$property}: {$value};";
    }
}
