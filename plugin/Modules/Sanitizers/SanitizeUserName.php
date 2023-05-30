<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserName extends StringSanitizer
{
    /**
     * \p{L} = any kind of letter from any language.
     * \p{N} = any kind of numeric character in any script.
     * @see https://www.regular-expressions.info/unicode.html
     */
    public function run(): string
    {
        $value = sanitize_text_field($this->value());
        $value = preg_replace('/[^\p{L}\p{N} ._\-]/u', '', $value);
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if (empty($value)) {
            $user = wp_get_current_user();
            if ($user->exists()) {
                return $user->display_name;
            }
        }
        return $value;
    }
}
