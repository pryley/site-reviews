<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttrStyle extends StringSanitizer
{
    public function run(): string
    {
        $value = strtolower($this->value());
        $parts = explode(';', $value);
        $parts = array_map(fn ($propVal) => $this->sanitizePropertyValue($propVal), $parts);
        $parts = array_filter($parts);
        $value = implode(';', $parts);
        return esc_attr($value);
    }

    protected function sanitizePropertyValue(string $propVal): string
    {
        $parts = explode(':', $propVal, 2);
        $property = preg_replace('/[^a-z\-]/', '', $parts[0] ?? '');
        $value = preg_replace('/[^\w,.!#%"()\-\'\/ ]/', '', $parts[1] ?? '');
        if (empty($property) || empty($value)) {
            return '';
        }
        return "{$property}:{$value}";
    }
}
