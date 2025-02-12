<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * Returns unslashed data.
 */
class SanitizeTextHtml extends StringSanitizer
{
    public function run(): string
    {
        $allowedKeys = array_filter($this->args) ?: [
            'a', 'em', 'mark', 'strong',
        ];
        $allowedHtml = Arr::restrictKeys(wp_kses_allowed_html('post'), $allowedKeys);
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        $value = $this->kses($this->value());
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = addslashes(wp_kses(stripslashes($value), $allowedHtml));
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_kses(stripslashes($value), $allowedHtml); // do this a second time to catch tags inside <script> tag
        return $value;
    }
}
