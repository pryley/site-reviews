<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;

class SanitizeAttrStyle extends StringSanitizer
{
    public function run(): string
    {
        $value = html_entity_decode($this->value(), ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_filter_nohtml_kses($value); // strip all html tags
        $value = strtolower($value);
        $parts = explode(';', $value);
        $parts = array_map(fn ($propVal) => $this->sanitizePropertyValue($propVal), $parts);
        $parts = array_filter($parts);
        $value = implode(' ', $parts);
        $value = trim($value);
        $value = safecss_filter_attr($value);
        return esc_attr($value);
    }

    protected function sanitizePropertyValue(string $propVal): string
    {
        $parts = explode(':', $propVal, 2);
        $parts = array_pad($parts, 2, '');
        $property = preg_replace('/[^a-z\-]/', '', $parts[0]);
        $value = preg_replace('/[^\w,.:!#%"()\-\'\/ ]/', '', $parts[1]);
        $value = trim($value);
        if (empty($property) || Helper::isEmpty($value)) {
            return '';
        }
        return "{$property}:{$value};";
    }
}
