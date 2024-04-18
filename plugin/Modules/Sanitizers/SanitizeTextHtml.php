<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SanitizeTextHtml extends StringSanitizer
{
    public function run(): string
    {
        $allowedKeys = array_filter($this->args) ?: [
            'a', 'em', 'mark', 'strong',
        ];
        $allowedHtml = Arr::restrictKeys(wp_kses_allowed_html('post'), $allowedKeys);
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        $value = wp_specialchars_decode($this->value());
        $value = wp_kses(wp_unslash($value), $allowedHtml);
        return $this->kses($value);
    }
}
