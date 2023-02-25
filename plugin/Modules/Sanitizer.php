<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

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

    public function run(): array
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
     */
    public function sanitizeArray($value): array
    {
        return Arr::consolidate($value);
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeArrayInt($value): array
    {
        return Arr::uniqueInt(Cast::toArray($value), true); // use absint
    }

    /**
     * @param mixed $value
     * @return string[]
     */
    public function sanitizeArrayString($value): array
    {
        $sanitized = array_filter(Cast::toArray($value), 'is_string');
        array_walk($sanitized, function (&$value) {
            $value = $this->sanitizeText($value);
        });
        return $sanitized;
    }

    /**
     * @param mixed $value
     */
    public function sanitizeAttr($value): string
    {
        $value = Cast::toString($value);
        return esc_attr($value);
    }

    /**
     * @param mixed $value
     */
    public function sanitizeAttrClass($value): string
    {
        $classes = Cast::toString($value);
        $classes = explode(' ', $classes);
        $classes = array_values(array_filter(array_unique($classes)));
        $classes = array_map('sanitize_html_class', $classes);
        $classes = implode(' ', $classes);
        return $classes;
    }

    /**
     * @param mixed $value
     */
    public function sanitizeAttrStyle($value): string
    {
        $style = Cast::toString($value);
        $style = strtolower($style);
        $style = preg_replace('/[^a-z0-9_%:;,.#"() \-\'\/]/', '', $style);
        return esc_attr($style);
    }

    /**
     * @param mixed $value
     */
    public function sanitizeBool($value): bool
    {
        return Cast::toBool($value);
    }

    /**
     * If date is invalid then return an empty string.
     * @param mixed $value
     */
    public function sanitizeDate($value, string $fallback = ''): string
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
     */
    public function sanitizeEmail($value): string
    {
        return sanitize_email(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     */
    public function sanitizeId($value): string
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
     */
    public function sanitizeInt($value): int
    {
        return Cast::toInt($value);
    }

    /**
     * @param mixed $value
     * @param mixed $max
     */
    public function sanitizeMax($value, $max = 0): int
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
     */
    public function sanitizeMin($value, $min = 0): int
    {
        return max(Cast::toInt($min), Cast::toInt($value));
    }

    /**
     * @param mixed $value
     */
    public function sanitizeJson($value): array
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
     */
    public function sanitizeKey($value): string
    {
        $value = sanitize_key($this->sanitizeText($value));
        return substr(Str::snakeCase($value), 0, 32); // limit the key to 32 characters
    }

    /**
     * This allows lowercase alpha and underscore characters.
     * @param mixed $value
     */
    public function sanitizeName($value): string
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
    public function sanitizePostIds($value): array
    {
        $postIds = Cast::toArray($value);
        $postIds = array_map([Helper::class, 'getPostId'], $postIds);
        return Arr::uniqueInt($postIds);
    }

    /**
     * @param mixed $value
     */
    public function sanitizeRating($value): int
    {
        $max = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        $min = max(0, (int) glsr()->constant('MIN_RATING', Rating::class));
        return max($min, min($max, Cast::toInt($value)));
    }

    /**
     * This allows lowercase alphannumeric, underscore, and dash characters.
     * @param mixed $value
     */
    public function sanitizeSlug($value): string
    {
        return sanitize_title($this->sanitizeText($value));
    }

    /**
     * @param mixed $value
     * @return int[]
     */
    public function sanitizeTermIds($value): array
    {
        $termIds = Cast::toArray($value);
        $termIds = array_map([Helper::class, 'getTermTaxonomyId'], $termIds);
        return Arr::uniqueInt($termIds);
    }

    /**
     * Strips all HTML from string.
     * @param mixed $value
     */
    public function sanitizeText($value): string
    {
        return sanitize_text_field(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     */
    public function sanitizeTextHtml($value): string
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
     */
    public function sanitizeTextMultiline($value): string
    {
        return sanitize_textarea_field(trim(Cast::toString($value)));
    }

    /**
     * Returns slashed data!
     * @param mixed $value
     */
    public function sanitizeTextPost($value): string
    {
        return wp_filter_post_kses(trim(Cast::toString($value)));
    }

    /**
     * @param mixed $value
     */
    public function sanitizeUrl($value): string
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
     */
    public function sanitizeUserEmail($value): string
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
     * @param int|string|null $fallbackUserId
     */
    public function sanitizeUserId($value, $fallbackUserId = null): int
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
    public function sanitizeUserIds($value): array
    {
        $userIds = Cast::toArray($value);
        $userIds = array_map([Helper::class, 'getUserId'], $userIds);
        return Arr::uniqueInt($userIds);
    }

    /**
     * @param mixed $value
     */
    public function sanitizeUserName($value): string
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
     */
    public function sanitizeVersion($value): string
    {
        $value = Cast::toString($value);
        if (1 === preg_match('/^(\d+\.)?(\d+\.)?(\d+)(-[a-z0-9]+)?$/i', $value)) {
            return $value;
        }
        return '';
    }

    protected function buildSanitizers(array $sanitizers): array
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
