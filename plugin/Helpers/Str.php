<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;

class Str
{
    public static function camelCase(string $string): string
    {
        $string = ucwords(str_replace(['-', '_'], ' ', trim($string)));
        return str_replace(' ', '', $string);
    }

    public static function contains(string $haystack, array $needles): bool
    {
        $needles = array_filter($needles, Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== $needle && str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns an unsanitized id attribute.
     */
    public static function convertNameToId(string $string, string $prefix = ''): string
    {
        $string = preg_replace('/[^a-z\d\[_]+/', '[', strtolower($string));
        $parts = explode('[', $string);
        $parts = array_filter([$prefix, ...$parts]);
        return implode('-', $parts);
    }

    public static function convertNameToPath(string $name): string
    {
        $parts = preg_split('/\[|\]/', $name);
        $parts = array_values(array_filter($parts));
        return implode('.', $parts);
    }

    public static function convertPathToName(string $path, string $prefix = ''): string
    {
        $levels = explode('.', $path);
        $levels = array_filter($levels);
        return array_reduce($levels, function ($carry, $value) {
            return empty($carry) ? $value : "{$carry}[{$value}]";
        }, $prefix);
    }

    public static function dashCase(string $string): string
    {
        return str_replace('_', '-', static::snakeCase($string));
    }

    public static function endsWith(string $haystack, array $needles): bool
    {
        $needles = array_filter($needles, Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== (string) $needle && str_ends_with((string) $haystack, (string) $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $value
     */
    public static function fallback($value, string $fallback): string
    {
        return is_scalar($value) && '' !== trim($value)
            ? Cast::toString($value)
            : Cast::toString($fallback);
    }

    public static function hash(string $value, int $maxLength = 32): string
    {
        require_once ABSPATH.WPINC.'/pluggable.php';
        return substr(wp_hash($value, 'nonce'), 0, max(8, $maxLength));
    }

    public static function join(array $values, bool $quoted = false): string
    {
        return $quoted
            ? "'".implode("','", $values)."'"
            : implode(', ', $values);
    }

    public static function mask(string $string, int $preserveStart = 0, int $preserveEnd = 0, int $maxLength = 13): string
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

    public static function naturalJoin(array $values): string
    {
        $and = __('and', 'site-reviews');
        $values[] = implode(" {$and} ", array_splice($values, -2));
        return implode(', ', $values);
    }

    public static function prefix(string $string, string $prefix, ?string $trim = null): string
    {
        if ('' === $string) {
            return $string;
        }
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix.trim(static::removePrefix($string, $trim));
    }

    public static function random(int $length = 8): string
    {
        $text = base64_encode(wp_generate_password());
        return substr(str_replace(['/', '+', '='], '', $text), 0, $length);
    }

    public static function removePrefix(string $string, string $prefix): string
    {
        return str_starts_with($string, $prefix)
            ? substr($string, strlen($prefix))
            : $string;
    }

    public static function removeSuffix(string $string, string $suffix): string
    {
        return str_ends_with($string, $suffix)
            ? substr($string, 0, strrpos($string, $suffix))
            : $string;
    }

    public static function replaceFirst(string $search, string $replace, string $subject): string
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

    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        $position = strrpos($subject, $search);
        if ('' !== $search && false !== $position) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }
        return $subject;
    }

    /**
     * @param string|string[] $restrictions
     */
    public static function restrictTo($restrictions, string $value, string $fallback = '', bool $strict = false): string
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

    public static function snakeCase(string $string): string
    {
        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', $string);
            $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
            $string = mb_strtolower($string, 'UTF-8');
        }
        return str_replace('-', '_', $string);
    }

    public static function startsWith(string $haystack, array $needles): bool
    {
        $needles = array_filter($needles, '\GeminiLabs\SiteReviews\Helper::isNotEmpty');
        foreach ($needles as $needle) {
            if ('' !== (string) $needle && str_starts_with((string) $haystack, (string) $needle)) {
                return true;
            }
        }
        return false;
    }

    public static function suffix(string $string, string $suffix): string
    {
        if (!str_ends_with($string, $suffix)) {
            return $string.$suffix;
        }
        return $string;
    }

    public static function titleCase(string $string): string
    {
        $value = str_replace(['-', '_'], ' ', $string);
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function truncate(string $value, int $length, string $end = ''): string
    {
        return mb_strwidth($value, 'UTF-8') > $length
            ? mb_substr($value, 0, $length, 'UTF-8').$end
            : $value;
    }
}
