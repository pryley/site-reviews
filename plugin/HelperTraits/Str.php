<?php

namespace GeminiLabs\SiteReviews\HelperTraits;

trait Str
{
    /**
     * @param string $string
     * @return string
     */
    public function camelCase($string)
    {
        $string = ucwords(str_replace(['-', '_'], ' ', trim($string)));
        return str_replace(' ', '', $string);
    }

    /**
     * @param string $name
     * @return string
     */
    public function convertPathToId($path, $prefix = '')
    {
        return str_replace(['[', ']'], ['-', ''], $this->convertPathToName($path, $prefix));
    }

    /**
     * @param string $path
     * @return string
     */
    public function convertPathToName($path, $prefix = '')
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
    public function dashCase($string)
    {
        return str_replace('_', '-', $this->snakeCase($string));
    }

    /**
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    public function endsWith($needle, $haystack)
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
    public function prefix($prefix, $string, $trim = null)
    {
        if (null === $trim) {
            $trim = $prefix;
        }
        return $prefix.trim($this->removePrefix($trim, $string));
    }

    /**
     * @param string $prefix
     * @param string $string
     * @return string
     */
    public function removePrefix($prefix, $string)
    {
        return $this->startsWith($prefix, $string)
            ? substr($string, strlen($prefix))
            : $string;
    }

    /**
     * @param string $string
     * @return string
     */
    public function snakeCase($string)
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
    public function startsWith($needle, $haystack)
    {
        return substr($haystack, 0, strlen($needle)) === $needle;
    }
}
