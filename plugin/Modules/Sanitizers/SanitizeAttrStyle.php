<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttrStyle extends StringSanitizer
{
    public function run(): string
    {
        $css = safecss_filter_attr($this->value());
        if ('' === $css) {
            return '';
        }
        $rules = [];
        foreach (explode(';', $css) as $declaration) {
            $parts = explode(':', trim($declaration), 2);
            [$property, $value] = array_pad($parts, 2, '');
            $property = trim(strtolower($property));
            if ('' === $property) {
                continue;
            }
            $value = trim($value);
            if ('' === $value) {
                unset($rules[$property]); // remove empty properties
                continue;
            }
            $rules[$property] = "{$property}:{$value}";
        }
        if (empty($rules)) {
            return '';
        }
        return esc_attr(implode(';', $rules).';');
    }
}
