<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Returns slashed data.
 */
class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        $value = html_entity_decode($this->value());
        $value = wp_filter_post_kses(wp_unslash($value));
        return $this->kses($value);
    }
}
