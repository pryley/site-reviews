<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeUserName extends StringSanitizer
{
    public array $args;
    public $value;
    public array $values;

    public function __construct($value, array $args = [], array $values = [])
    {
        $args = array_pad($args, 2, ''); // minimum of 2 args
        $this->args = $args;
        $this->value = $this->userValue($value);
        $this->values = $values;
    }

    public function run(): string
    {
        $value = $this->sanitizeValue($this->value()); // allow any valid name value
        if (defined('WP_IMPORTING')) {
            return $value;
        }
        if ('' !== $value) { // allow "0"
            return $value;
        }
        $fallback = Cast::toString($this->args[0]); // try the fallback value
        if ('current_user' === $fallback) {
            $fallback = $this->userValue(wp_get_current_user());
        }
        return $this->sanitizeValue($fallback);
    }

    /**
     * \p{L} = any kind of letter from any language.
     * \p{M} = any character intended to be combined with another character (e.g. accents, umlauts, enclosing boxes, etc.).
     * \p{N} = any kind of numeric character in any script.
     * \p{Pf} = any kind of closing quote.
     *
     * @see https://www.regular-expressions.info/unicode.html
     */
    protected function sanitizeValue(string $value): string
    {
        if ('' === $value) {
            return $value;
        }
        $name = strstr($value, '@', true) ?: $value; // prevent exposing user email
        $name = $this->kses($name);
        $name = html_entity_decode($name, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
        $name = wp_specialchars_decode($name); // &lt; => <
        $name = wp_strip_all_tags($name);
        $name = preg_replace('/%([a-fA-F0-9][a-fA-F0-9])/', '', $name); // Remove percent-encoded characters.
        $name = preg_replace('/&.+?;/', '', $name); // Remove HTML entities.
        $name = preg_replace('/[^\p{L}\p{M}\p{N}\p{Pf}\'\.\,\- ]/u', '', $name);
        $name = sanitize_text_field($name); // Remove extra whitespace.
        return $name;
    }

    /**
     * @param mixed $user
     */
    protected function userValue($user): string
    {
        if (!$user instanceof \WP_User) {
            return Cast::toString($user);
        }
        if (!$user->exists()) {
            return '';
        }
        return $user->display_name ?: $user->user_nicename ?: $user->user_login;
    }
}
