<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;

class Sanitizer
{
    public const JSON_ERROR_CODES = [
        JSON_ERROR_DEPTH => 'JSON Error: The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'JSON Error: Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'JSON Error: Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'JSON Error: Syntax error',
        JSON_ERROR_UTF8 => 'JSON Error: Malformed UTF-8 characters, possibly incorrectly encoded',
        JSON_ERROR_RECURSION => 'JSON Error: One or more recursive references in the value to be encoded',
        JSON_ERROR_INF_OR_NAN => 'JSON Error: One or more NAN or INF values in the value to be encoded',
        JSON_ERROR_UNSUPPORTED_TYPE => 'JSON Error: A value of a type that cannot be encoded was given',
        JSON_ERROR_INVALID_PROPERTY_NAME => 'JSON Error: A property name that cannot be encoded was given',
        JSON_ERROR_UTF16 => 'JSON Error: Malformed UTF-16 characters, possibly incorrectly encoded',
    ];

    /**
     * @var array
     */
    public $sanitizers;

    /**
     * @var array
     */
    public $values;

    public function __construct(array $values = [], array $sanitizers = [])
    {
        $this->sanitizers = $this->buildSanitizers(Arr::consolidate($sanitizers));
        $this->values = Arr::consolidate($values);
    }

    /**
     * @return array|bool|string
     */
    public function run()
    {
        $results = $this->values;
        foreach ($this->values as $key => $value) {
            if (!array_key_exists($key, $this->sanitizers)) {
                continue;
            }
            foreach ($this->sanitizers[$key] as $sanitizer) {
                $args = $sanitizer['args'];
                $method = $sanitizer['method'];
                $value = call_user_func([$this, $method], $value, ...$args);
            }
            $results[$key] = $value;
        }
        return $results;
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function sanitizeArray($value)
    {
        return Arr::consolidate($value);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeArrayInt($value)
    {
        return Arr::uniqueInt(Cast::toArray($value), true); // use absint
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function sanitizeArrayString($value)
    {
        $sanitized = array_filter(Cast::toArray($value), 'is_string');
        array_walk($sanitized, function (&$value) {
            $value = $this->sanitizeText($value);
        });
        return $sanitized;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function sanitizeBool($value)
    {
        return Cast::toBool($value);
    }

    /**
     * If date is invalid then return an empty string.
     * @param mixed $value
     * @param string $fallback
     * @return string
     */
    public function sanitizeDate($value, $fallback = '')
    {
        $date = trim(Cast::toString($value));
        $format = 'Y-m-d H:i:s';
        $formattedDate = \DateTime::createFromFormat($format, $date);
        if ($formattedDate && $formattedDate->format($format) === $date) {
            return $date;
        }
        $timestamp = strtotime($date);
        if (false === $timestamp) {
            return $fallback;
        }
        $date = wp_date('Y-m-d H:i:s', $timestamp);
        if (false === $date) {
            return $fallback;
        }
        return $date;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeEmail($value)
    {
        return sanitize_email(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeId($value)
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        $value = sanitize_key($this->sanitizeText($value));
        $value = substr($value, 0, 32); // limit the id to 32 characters
        if (empty($value)) {
            $value = glsr()->prefix.substr(wp_hash(serialize($this->values), 'nonce'), -12, 8);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return int
     */
    public function sanitizeInt($value)
    {
        return Cast::toInt($value);
    }

    /**
     * @param mixed $value
     * @param mixed $max
     * @return int
     */
    public function sanitizeMax($value, $max = 0)
    {
        $max = Cast::toInt($max);
        $value = Cast::toInt($value);
        return $max > 0
            ? min($max, $value)
            : $value;
    }

    /**
     * @param mixed $value
     * @param mixed $min
     * @return int
     */
    public function sanitizeMin($value, $min = 0)
    {
        return max(Cast::toInt($min), Cast::toInt($value));
    }

    /**
     * @param mixed $value
     * @return array
     */
    public function sanitizeJson($value)
    {
        $result = '';
        if (is_scalar($value) && !Helper::isEmpty($value)) {
            $result = trim((string) $value);
            $result = htmlspecialchars_decode($result);
            $result = json_decode($result, true);
            $error = json_last_error();
            if (array_key_exists($error, static::JSON_ERROR_CODES)) {
                glsr_log()->error(static::JSON_ERROR_CODES[$error])->debug($value);
            }
        }
        return wp_unslash(Arr::consolidate($result));
    }

    /**
     * This allows lowercase alphannumeric and underscore characters.
     * @param mixed $value
     * @return string
     */
    public function sanitizeKey($value)
    {
        $value = sanitize_key($this->sanitizeText($value));
        return substr(Str::snakeCase($value), 0, 32); // limit the key to 32 characters
    }

    /**
     * This allows lowercase alpha and underscore characters.
     * @param mixed $value
     * @return string
     */
    public function sanitizeName($value)
    {
        $value = Str::snakeCase($this->sanitizeText($value));
        return preg_replace('/[^a-z_]/', '', $value);
    }

    /**
     * @param mixed $value
     * @return int|string
     */
    public function sanitizeNumeric($value)
    {
        return is_numeric($value) ? Cast::toInt($value) : '';
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizePostIds($value)
    {
        $postIds = Cast::toArray($value);
        foreach ($postIds as &$postId) {
            if ($sanitizedId = Helper::getPostId($postId)) {
                $postId = $sanitizedId;
            }
        }
        return Arr::uniqueInt($postIds);
    }

    /**
     * @param mixed $value
     * @return int
     */
    public function sanitizeRating($value)
    {
        $max = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        $min = max(0, (int) glsr()->constant('MIN_RATING', Rating::class));
        return max($min, min($max, Cast::toInt($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeSlug($value)
    {
        return sanitize_title($this->sanitizeText($value));
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeTermIds($value)
    {
        $termIds = Cast::toArray($value);
        foreach ($termIds as &$termId) {
            if ($sanitizedId = Helper::getTermTaxonomyId($termId)) {
                $termId = $sanitizedId;
            }
        }
        return Arr::uniqueInt($termIds);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeText($value)
    {
        return sanitize_text_field(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextHtml($value)
    {
        $allowedHtmlPost = wp_kses_allowed_html('post');
        $allowedHtml = [
            'a' => glsr_get($allowedHtmlPost, 'a'),
            'em' => glsr_get($allowedHtmlPost, 'em'),
            'strong' => glsr_get($allowedHtmlPost, 'strong'),
        ];
        $allowedHtml = glsr()->filterArray('sanitize/allowed-html', $allowedHtml, $this);
        return wp_kses($this->sanitizeTextMultiline($value), $allowedHtml);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextMultiline($value)
    {
        return sanitize_textarea_field(trim(Cast::toString($value)));
    }

    /**
     * Returns slashed data!
     * @param mixed $value
     * @return string
     */
    public function sanitizeTextPost($value)
    {
        return wp_filter_post_kses(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUrl($value)
    {
        $value = trim(Cast::toString($value));
        if (!Str::startsWith($value, 'http://, https://')) {
            $value = Str::prefix($value, 'https://');
        }
        $url = esc_url_raw($value);
        if (mb_strtolower($value) === mb_strtolower($url) && false !== filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }
        return '';
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUserEmail($value)
    {
        $user = wp_get_current_user();
        $value = $this->sanitizeEmail($value);
        if (!defined('WP_IMPORTING') && $user->exists()) {
            return Helper::ifEmpty($value, $user->user_email);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @param mixed $fallbackUserId
     * @return int
     */
    public function sanitizeUserId($value, $fallbackUserId = null)
    {
        $user = get_user_by('ID', Cast::toInt($value));
        if (false !== $user) {
            return (int) $user->ID;
        }
        if (defined('WP_IMPORTING')) {
            return 0;
        }
        if (is_null($fallbackUserId)) {
            return get_current_user_id();
        }
        $fallbackUser = get_user_by('ID', Cast::toInt($fallbackUserId));
        if (false !== $fallbackUser) {
            return (int) $fallbackUser->ID;
        }
        return 0;
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeUserIds($value)
    {
        $userIds = Cast::toArray($value);
        foreach ($userIds as &$userId) {
            if ($sanitizedId = Helper::getUserId($userId)) {
                $userId = $sanitizedId;
            }
        }
        return Arr::uniqueInt($userIds);
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeUserName($value)
    {
        $user = wp_get_current_user();
        $value = $this->sanitizeText($value);
        if (!defined('WP_IMPORTING') && $user->exists()) {
            return Helper::ifEmpty($value, $user->display_name);
        }
        return $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public function sanitizeVersion($value)
    {
        if (1 === preg_match('/^(\d+\.)?(\d+\.)?(\d+)(-[a-z0-9]+)?$/i', $value)) {
            return $value;
        }
        return '';
    }

    /**
     * @return array
     */
    protected function buildSanitizers(array $sanitizers)
    {
        $fallback = [ // fallback to this
            'args' => [],
            'method' => 'sanitizeText',
        ];
        foreach ($sanitizers as $key => $value) {
            $methods = Arr::consolidate(preg_split('/\|/', $value, -1, PREG_SPLIT_NO_EMPTY));
            $sanitizers[$key] = [];
            if (empty($methods)) {
                $sanitizers[$key][] = $fallback;
                continue;
            }
            foreach ($methods as $method) {
                $parts = preg_split('/:/', $method, 2, PREG_SPLIT_NO_EMPTY);
                $args = trim(Arr::get($parts, 1));
                $name = trim(Arr::get($parts, 0));
                $sanitizer = [
                    'args' => explode(',', $args),
                    'method' => Helper::buildMethodName($name, 'sanitize'),
                ];
                $sanitizers[$key][] = $sanitizer;
            }
        }
        return $sanitizers;
    }
}
