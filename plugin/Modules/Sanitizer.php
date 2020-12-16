<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class Sanitizer
{
    /**
     * @var array
     */
    protected $sanitizers;

    /**
     * @var array
     */
    protected $values;

    public function __construct($values, $sanitizers)
    {
        $this->sanitizers = $this->buildSanitizers(Arr::consolidate($sanitizers));
        $this->values = Arr::consolidate($values);
    }

    /**
     * @return array|bool|string
     */
    public function run()
    {
        $result = $this->values;
        foreach ($this->values as $key => $value) {
            if (array_key_exists($key, $this->sanitizers)) {
                $result[$key] = call_user_func([$this, $this->sanitizers[$key]], $value);
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function buildSanitizers(array $sanitizers)
    {
        foreach ($sanitizers as $key => &$type) {
            $method = Helper::buildMethodName($type, 'sanitize');
            $type = method_exists($this, $method)
                ? $method
                : 'sanitizeText';
        }
        return $sanitizers;
    }

    /**
     * @param mixed $value
     * @return array
     */
    protected function sanitizeArray($value)
    {
        return Arr::consolidate($value);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    protected function sanitizeArrayInt($value)
    {
        return Arr::uniqueInt(Cast::toArray($value));
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    protected function sanitizeArrayString($value)
    {
        return array_filter(Cast::toArray($value), 'is_string');
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function sanitizeBool($value)
    {
        return Cast::toBool($value);
    }

    /**
     * If date is invalid then return an empty string.
     * @param mixed $value
     * @return string
     */
    protected function sanitizeDate($value)
    {
        $date = strtotime(Cast::toString($value));
        if (false !== $date) {
            return wp_date('Y-m-d H:i:s', $date);
        }
        return '';
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeEmail($value)
    {
        return sanitize_email(Cast::toString($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeId($value)
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $value = $this->sanitizeSlug($value);
        if (empty($value)) {
            $value = glsr()->prefix.substr(wp_hash(serialize($this->values), 'nonce'), -12, 8);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return int
     */
    protected function sanitizeInt($value)
    {
        return Cast::toInt($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeKey($value)
    {
        return Str::snakeCase(sanitize_key($this->sanitizeText($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeSlug($value)
    {
        return sanitize_title($this->sanitizeText($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeText($value)
    {
        return sanitize_text_field(Cast::toString($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeTextMultiline($value)
    {
        return sanitize_textarea_field(Cast::toString($value));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeUrl($value)
    {
        $url = Cast::toString($value);
        if (!Str::startsWith('http://, https://', $url)) {
            $url = Str::prefix($url, 'https://');
        }
        $url = wp_http_validate_url($url);
        return esc_url_raw(Cast::toString($url));
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeUserEmail($value)
    {
        $user = wp_get_current_user();
        $value = Cast::toString($value);
        if ($user->exists() && !glsr()->retrieveAs('bool', 'import', false)) {
            return Helper::ifEmpty($value, $user->user_email);
        }
        return sanitize_email($value);
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function sanitizeUserName($value)
    {
        $user = wp_get_current_user();
        $value = Cast::toString($value);
        if ($user->exists() && !glsr()->retrieveAs('bool', 'import', false)) {
            return Helper::ifEmpty($value, $user->display_name);
        }
        return sanitize_text_field($value);
    }
}
