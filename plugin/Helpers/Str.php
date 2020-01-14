<?php

namespace GeminiLabs\SiteReviews\Helpers;

class Str
{
    /**
     * @param string $subject
     * @param string $search
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        return '' === $search
            ? $subject
            : array_reverse(explode($search, $subject))[0];
    }

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
     * @param string $needle
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        return false !== strpos($haystack, $needle);
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
        return array_reduce($matches[0], function ($carry, $word) use ($initialPunctuation) {
            return $carry.strtoupper(substr($word, 0, 1)).$initialPunctuation;
        });
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
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public static function endsWith($needle, $haystack)
    {
        $length = strlen($needle);
        return 0 != $length
            ? substr($haystack, -$length) === $needle
            : true;
    }

    /**
     * @param string $prefix
     * @param string $string
     * @param string|null $trim
     * @return string
     */
    public static function prefix($prefix, $string, $trim = null)
    {
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix.trim(static::removePrefix($trim, $string));
    }

    /**
     * @param string $prefix
     * @param string $string
     * @return string
     */
    public static function removePrefix($prefix, $string)
    {
        return static::startsWith($prefix, $string)
            ? substr($string, strlen($prefix))
            : $string;
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
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public static function startsWith($needle, $haystack)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
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
