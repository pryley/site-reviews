<?php

namespace GeminiLabs\SiteReviews\Modules\Sanitizers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SanitizeJson extends ArraySanitizer
{
    public const ERROR_CODES = [
        JSON_ERROR_CTRL_CHAR => 'JSON Error: Control character error, possibly incorrectly encoded',
        JSON_ERROR_DEPTH => 'JSON Error: The maximum stack depth has been exceeded',
        JSON_ERROR_INF_OR_NAN => 'JSON Error: One or more NAN or INF values in the value to be encoded',
        JSON_ERROR_INVALID_PROPERTY_NAME => 'JSON Error: A property name that cannot be encoded was given',
        JSON_ERROR_RECURSION => 'JSON Error: One or more recursive references in the value to be encoded',
        JSON_ERROR_STATE_MISMATCH => 'JSON Error: Invalid or malformed JSON',
        JSON_ERROR_SYNTAX => 'JSON Error: Syntax error',
        JSON_ERROR_UNSUPPORTED_TYPE => 'JSON Error: A value of a type that cannot be encoded was given',
        JSON_ERROR_UTF16 => 'JSON Error: Malformed UTF-16 characters, possibly incorrectly encoded',
        JSON_ERROR_UTF8 => 'JSON Error: Malformed UTF-8 characters, possibly incorrectly encoded',
    ];

    public function run(): array
    {
        $result = $this->value;
        if (is_scalar($this->value) && !Helper::isEmpty($this->value)) {
            $result = trim(Cast::toString($this->value));
            $result = html_entity_decode($result, ENT_QUOTES, 'UTF-8'); // &amp;lt => &lt;
            $result = wp_specialchars_decode($result); // &lt; => <
            $result = json_decode($result, true); // associative array!
            $error = json_last_error();
            if (array_key_exists($error, static::ERROR_CODES)) {
                glsr_log()->error(static::ERROR_CODES[$error])->debug($this->value);
                $result = [];
            }
        }
        return wp_unslash(Arr::consolidate($result));
    }
}
