<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Returns slashed data.
 */
class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->kses($this->value());
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = wp_filter_post_kses($value);
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_filter_post_kses($value); // do this a second time to catch tags inside <script> tag
        return $value;
    }
}
