<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeUserName extends StringSanitizer
{
    public function run(): string
    {
        $value = $this->sanitizeDisplayName($this->value());
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if ('' !== $value) {
            return $value;
        }
        $value = Cast::toString($this->args[0]); // try the fallback value
        if ('current_user' === $value) {
            $user = wp_get_current_user();
            if (!$user->exists()) {
                return '';
            }
            $value = $user->display_name ?: $user->user_nicename;
        }
        return $this->sanitizeDisplayName($value);
    }

    /**
     * \p{L} = any kind of letter from any language.
     * \p{M} = any character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
     * \p{N} = any kind of numeric character in any script.
     * \p{Pf} = any kind of closing quote.
     *
     * @see https://www.regular-expressions.info/unicode.html
     */
    protected function sanitizeDisplayName(string $value): string
    {
        if ('' === $value) {
            return $value;
        }
        $value = $this->kses($value);
        $value = html_entity_decode($value, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $value = wp_specialchars_decode($value); // &lt; => <
        $value = wp_strip_all_tags($value);
        $value = preg_replace('/%([a-fA-F0-9][a-fA-F0-9])/', '', $value); // Remove percent-encoded characters.
        $value = preg_replace('/&.+?;/', '', $value); // Remove HTML entities.
        $value = preg_replace('/[^\p{L}\p{M}\p{N}\p{Pf}\'\.\,\- ]/u', '', $value);
        $value = sanitize_text_field($value); // Remove extra whitespace.
        return $value;
    }
}
