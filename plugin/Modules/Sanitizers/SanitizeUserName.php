<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

class SanitizeUserName extends StringSanitizer
{
    /**
     * \p{L} = any kind of letter from any language.
     * \p{M} = any character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
     * \p{N} = any kind of numeric character in any script.
     * \p{Pf} = any kind of closing quote.
     * @see https://www.regular-expressions.info/unicode.html
     */
    public function run(): string
    {
        $value = wp_strip_all_tags($this->value());
        $value = preg_replace('/%([a-fA-F0-9][a-fA-F0-9])/', '', $value); // Remove percent-encoded characters.
        $value = preg_replace('/&.+?;/', '', $value); // Remove HTML entities.
        $value = preg_replace('/[^\p{L}\p{M}\p{N}\p{Pf}\'\.\,\- ]/u', '', $value);
        $value = sanitize_text_field($value); // Remove extra whitespace.
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
