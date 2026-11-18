<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

/**
 * Returns slashed data.
 */
class SanitizeTextPost extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->kses($this->decode($this->value()));
        for ($pass = 0; $pass < 10; ++$pass) { // a tag that is removed can leave another one behind
            $sanitized = wp_filter_post_kses($this->decode($value));
            if ($sanitized === $value) {
                break;
            }
            $value = $sanitized;
        }
        return $value;
    }
}
