<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;

class Str
{
    /**
     * @param string $string
     * @return string
     */
    public static function camelCase($string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', trim($string)));
        return str_replace(' ', '', $string);
    }

    /**
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== $needle && str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public static function convertPathToId($path, $prefix = '')
    {
        return str_replace(['[', ']'], ['-', ''], static::convertPathToName($path, $prefix));
    }

    /**
     * @param string $path
     * @param string $prefix
     * @return string
     */
    public static function convertPathToName($path, $prefix = '')
    {
        $levels = explode('.', $path);
        return array_reduce($levels, function ($result, $value) {
            return $result .= '['.$value.']';
        }, $prefix);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function dashCase($string)
    {
        return str_replace('_', '-', static::snakeCase($string));
    }

    /**
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== (string) $needle && str_ends_with((string) $haystack, (string) $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     * @param string $fallback
     * @return string
     */
    public static function fallback($value, $fallback)
    {
        return is_scalar($value) && '' !== trim($value)
            ? Cast::toString($value)
            : Cast::toString($fallback);
    }

    /**
     * @param bool $quoted
     * @return string
     */
    public static function join(array $values, $quoted = false)
    {
        return $quoted
            ? "'".implode("','", $values)."'"
            : implode(', ', $values);
    }

    /**
     * @param string $string
     * @param int $preserveStart
     * @param int $preserveEnd
     * @param int $maxLength
     * @return string
     */
    public static function mask($string, $preserveStart = 0, $preserveEnd = 0, $maxLength = 13)
    {
        $encoding = 'UTF-8';
        if (empty($string)) {
            return $string;
        }
        $startLength = max(0, $preserveStart);
        $endLength = max(0, $preserveEnd);
        $start = mb_substr($string, 0, $startLength, $encoding);
        $end = mb_substr($string, -$endLength, $endLength);
        $segmentLen = max($maxLength - ($startLength + $endLength), 0);
        if (0 === $segmentLen) {
            return $string;
        }
        return $start.str_repeat(mb_substr('*', 0, 1, $encoding), $segmentLen).$end;
    }

    /**
     * @return string
     */
    public static function naturalJoin(array $values)
    {
        $and = __('and', 'site-reviews');
        $values[] = implode(' '.$and.' ', array_splice($values, -2));
        return implode(', ', $values);
    }

    /**
     * @param string $string
     * @param string $prefix
     * @param string|null $trim
     * @return string
     */
    public static function prefix($string, $prefix, $trim = null)
    {
        if ('' === $string) {
            return $string;
        }
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix.trim(static::removePrefix($string, $trim));
    }

    /**
     * @param int $length
     * @return string
     */
    public static function random($length = 8)
    {
        $text = base64_encode(wp_generate_password());
        return substr(str_replace(['/', '+', '='], '', $text), 0, $length);
    }

    /**
     * @param string $string
     * @param string $prefix
     * @return string
     */
    public static function removePrefix($string, $prefix)
    {
        return static::startsWith($string, $prefix)
            ? substr($string, strlen($prefix))
            : $string;
    }

    /**
     * @param string $string
     * @param string $suffix
     * @return string
     */
    public static function removeSuffix($string, $suffix)
    {
        return static::endsWith($string, $suffix)
            ? substr($string, 0, strrpos($string, $suffix))
            : $string;
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject)
    {
        if ('' === $search) {
            return $subject;
        }
        $position = strpos($subject, $search);
        if (false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        return $subject;
    }

    /**
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @return string
     */
    public static function replaceLast($search, $replace, $subject)
    {
        $position = strrpos($subject, $search);
        if ('' !== $search && false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        return $subject;
    }

    /**
     * @param string|string[] $restrictions
     * @param string $value
     * @param string $fallback
     * @param bool $strict
     * @return string
     */
    public static function restrictTo($restrictions, $value, $fallback = '', $strict = false)
    {
        $needle = $value;
        $haystack = Cast::toArray($restrictions);
        if (true !== $strict) {
            $needle = strtolower($needle);
            $haystack = array_map('strtolower', $haystack);
        }
        return in_array($needle, $haystack)
            ? $value
            : $fallback;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function snakeCase($string)
    {
        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', $string);
            $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
            $string = mb_strtolower($string, 'UTF-8');
        }
        return str_replace('-', '_', $string);
    }

    /**
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles)
    {
        $needles = array_filter(Cast::toArray($needles), '\GeminiLabs\SiteReviews\Helper::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== (string) $needle && str_starts_with((string) $haystack, (string) $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $string
     * @param string $suffix
     * @return string
     */
    public static function suffix($string, $suffix)
    {
        if (!static::endsWith($string, $suffix)) {
            return $string.$suffix;
        }
        return $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function titleCase($string)
    {
        $value = str_replace(['-', '_'], ' ', $string);
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param string $value
     * @param int $length
     * @param string $end
     * @return string
     */
    public static function truncate($value, $length, $end = '')
    {
        return mb_strwidth($value, 'UTF-8') > $length
            ? mb_substr($value, 0, $length, 'UTF-8').$end
            : $value;
    }
}
