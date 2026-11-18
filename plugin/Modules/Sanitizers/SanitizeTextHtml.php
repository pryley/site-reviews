<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * Returns unslashed data.
 */
class SanitizeTextHtml extends StringSanitizer
{
    public function allowedHtml(): array
    {
        $allowedKeys = array_filter($this->args) ?: [
            'a', 'em', 'mark', 'strong',
        ];
        $allowedHtml = Arr::restrictKeys(wp_kses_allowed_html('post'), $allowedKeys);
        return glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
    }

    public function run(): string
    {
        $allowedHtml = $this->allowedHtml();
        $value = stripslashes($this->kses($this->decode($this->value())));
        for ($pass = 0; $pass < 10; ++$pass) { // a tag that is removed can leave another one behind
            $sanitized = wp_kses($this->decode($value), $allowedHtml);
            if ($sanitized === $value) {
                break;
            }
            $value = $sanitized;
        }
        return $value;
    }
}
