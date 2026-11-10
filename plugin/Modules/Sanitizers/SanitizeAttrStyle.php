<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeAttrStyle extends StringSanitizer
{
    /**
     * @param string $css the declaration without the functions WordPress allows
     *
     * @filter safecss_filter_attr_allow_css
     */
    public function allowColorFunctions($isAllowed, $css): bool
    {
        if ($isAllowed) {
            return true;
        }
        // rgb(), rgba(), hsl() and hsla() that hold numbers, percentages, angles and separators
        $css = preg_replace('/\b(?:rgb|hsl)a?\((?:[\d\s.,%\/+-]|deg|grad|rad|turn|none)+\)/i', '', (string) $css);
        return 0 === preg_match('%[\\\(&=}]|/\*%', (string) $css); // the test WordPress runs, on what is left
    }

    public function run(): string
    {
        add_filter('safecss_filter_attr_allow_css', [$this, 'allowColorFunctions'], 10, 2);
        $css = safecss_filter_attr($this->value());
        remove_filter('safecss_filter_attr_allow_css', [$this, 'allowColorFunctions'], 10);
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
