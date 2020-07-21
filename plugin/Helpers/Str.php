<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;

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
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function contains($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (false !== strpos($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @param string $nameType first|first_initial|initials|last|last_initial
     * @param string $initialType period|period_space|space
     * @return string
     */
    public static function convertName($name, $nameType = '', $initialType = '')
    {
        $names = preg_split('/\W/', $name, 0, PREG_SPLIT_NO_EMPTY);
        $firstName = array_shift($names);
        $lastName = array_pop($names);
        $initialTypes = [
            'period' => '.',
            'period_space' => '. ',
            'space' => ' ',
        ];
        $initialPunctuation = (string) Arr::get($initialTypes, $initialType, ' ');
        if ('initials' == $nameType) {
            return static::convertToInitials($name, $initialPunctuation);
        }
        $nameTypes = [
            'first' => $firstName,
            'first_initial' => substr($firstName, 0, 1).$initialPunctuation.$lastName,
            'last' => $lastName,
            'last_initial' => $firstName.' '.substr($lastName, 0, 1).$initialPunctuation,
        ];
        return trim((string) Arr::get($nameTypes, $nameType, $name));
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
     * @param string $name
     * @param string $initialPunctuation
     * @return string
     */
    public static function convertToInitials($name, $initialPunctuation = '')
    {
        preg_match_all('/(?<=\s|\b)\pL/u', $name, $matches);
        $result = array_reduce($matches[0], function ($carry, $word) use ($initialPunctuation) {
            return $carry.strtoupper(substr($word, 0, 1)).$initialPunctuation;
        });
        return trim($result);
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
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function endsWith($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (substr($haystack, -strlen(Cast::toString($needle))) === $needle) {
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
        return is_scalar($value) && !empty(trim($value))
            ? Cast::toString($value)
            : Cast::toString($fallback);
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
        if (empty($string)) {
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
        return substr(str_replace(['/','+','='], '', $text), 0, $length);
    }

    /**
     * @param string $prefix
     * @param string $string
     * @return string
     */
    public static function removePrefix($string, $prefix)
    {
        return static::startsWith($prefix, $string)
            ? substr($string, strlen($prefix))
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
        if ($search == '') {
            return $subject;
        }
        $position = strpos($subject, $search);
        if ($position !== false) {
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
        if ($position !== false) {
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
            $string = function_exists('mb_strtolower')
                ? mb_strtolower($string, 'UTF-8')
                : strtolower($string);
        }
        return str_replace('-', '_', $string);
    }

    /**
     * @param string|string[] $needles
     * @param string $haystack
     * @return bool
     */
    public static function startsWith($needles, $haystack)
    {
        $needles = array_filter(Cast::toArray($needles), Helper::class.'::isNotEmpty');
        foreach ($needles as $needle) {
            if (substr($haystack, 0, strlen(Cast::toString($needle))) === $needle) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $string
     * @param int $length
     * @return string
     */
    public static function truncate($string, $length)
    {
        return strlen($string) > $length
            ? substr($string, 0, $length)
            : $string;
    }
}
