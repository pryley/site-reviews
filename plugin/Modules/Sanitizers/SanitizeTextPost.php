<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Returns slashed data.
 */
class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        $value = html_entity_decode($this->value(), ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_filter_post_kses(wp_unslash($value));
        return $this->kses($value);
    }
}
