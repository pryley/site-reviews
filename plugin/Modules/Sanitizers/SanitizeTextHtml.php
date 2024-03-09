<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeTextHtml extends StringSanitizer
{
    public function run(): string
    {
        $allowed = [
            'a', 'em', 'mark', 'strong',
        ];
        $allowedHtml = wp_kses_allowed_html('post');
        $allowedHtml = array_intersect_key($allowedHtml, array_fill_keys($allowed, ''));
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        $value = html_entity_decode($this->value());
        $value = wp_kses(wp_unslash($value), $allowedHtml);
        return $this->kses($value);
    }
}
