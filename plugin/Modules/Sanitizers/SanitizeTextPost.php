<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Returns slashed data.
 */
class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        return $this->kses(wp_filter_post_kses(wp_unslash($this->value())));
    }
}
