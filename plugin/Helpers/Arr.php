<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;

class Arr
{
    public static function compare(array $arr1, array $arr2): bool
    {
        sort($arr1);
        sort($arr2);
        return $arr1 == $arr2;
    }

    /**
     * @param mixed $value
     */
    public static function consolidate($value, array $fallback = []): array
    {
        if ($value instanceof Arguments) {
            return $value->getArrayCopy(); // This ensures we don't convert array values.
        }
        if (is_object($value)) {
            $values = get_object_vars($value);
            $value = Helper::ifEmpty($values, (array) $value, $strict = true);
        }
        return is_array($value) ? $value : $fallback;
    }

    /**
     * @param mixed $value
     * @param mixed $callback
     */
    public static function convertFromString($value, $callback = null): array
    {
        if (is_scalar($value)) {
            $value = array_map('trim', explode(',', Cast::toString($value)));
        }
        $callback = Helper::ifEmpty(Cast::toString($callback), Helper::class.'::isNotEmpty');
        return static::reindex(array_filter((array) $value, $callback));
    }

    public static function flatten(array $array, bool $flattenValue = false, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = ltrim("{$prefix}.{$key}", '.');
            if (static::isIndexedAndFlat($value)) {
                $value = Helper::ifTrue(!$flattenValue, $value,
                    fn () => '['.implode(', ', $value).']'
                );
            } elseif (is_array($value)) {
                $result = array_merge($result, static::flatten($value, $flattenValue, $newKey));
                continue;
            }
            $result[$newKey] = $value;
        }
        return $result;
    }

    /**
     * Get a value from an array of values using a dot-notation path as reference.
     *
     * @param mixed      $data
     * @param string|int $path
     * @param mixed      $fallback
     *
     * @return mixed
     */
    public static function get($data, $path = '', $fallback = '')
    {
        $data = static::consolidate($data);
        $keys = explode('.', (string) $path);
        $result = $fallback;
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return $fallback;
            }
            if (is_object($data[$key])) {
                $result = $data[$key];
                $data = static::consolidate($result);
                continue;
            }
            $result = $data[$key];
            $data = $result;
        }
        return $result;
    }

    /**
     * @param mixed      $data
     * @param string|int $path
     * @param mixed      $fallback
     *
     * @return mixed
     */
    public static function getAs(string $cast, $data, $path = '', $fallback = '')
    {
        return Cast::to($cast, static::get($data, $path, $fallback));
    }

    /**
     * @param string|int $key
     */
    public static function insertAfter($key, array $array, array $insert): array
    {
        return static::insert($array, $insert, $key, 'after');
    }

    /**
     * @param string|int $key
     */
    public static function insertBefore($key, array $array, array $insert): array
    {
        return static::insert($array, $insert, $key, 'before');
    }

    /**
     * @param string|int $key
     */
    public static function insert(array $array, array $insert, $key, string $position = 'before'): array
    {
        $keyPosition = array_search($key, array_keys($array));
        if (false !== $keyPosition) {
            $keyPosition = Cast::toInt($keyPosition);
            if ('after' === $position) {
                ++$keyPosition;
            }
            $result = array_slice($array, 0, $keyPosition);
            $result = array_merge($result, $insert);
            return array_merge($result, array_slice($array, $keyPosition));
        }
        return array_merge($array, $insert);
    }

    /**
     * @param mixed $array
     */
    public static function isIndexedAndFlat($array): bool
    {
        if (!is_array($array) || array_filter($array, 'is_array')) {
            return false;
        }
        return wp_is_numeric_array($array);
    }

    public static function prefixKeys(array $values, string $prefix = '_', bool $prefixed = true): array
    {
        $trim = Helper::ifTrue($prefixed, $prefix, '');
        $prefixed = [];
        foreach ($values as $key => $value) {
            $key = trim($key);
            if (0 === strpos($key, $prefix)) {
                $key = substr($key, strlen($prefix));
            }
            $prefixed[$trim.$key] = $value;
        }
        return $prefixed;
    }

    /**
     * @param mixed $value
     * @param mixed $key
     */
    public static function prepend(array $array, $value, $key = null): array
    {
        if (!is_null($key)) {
            return [$key => $value] + $array;
        }
        array_unshift($array, $value);
        return $array;
    }

    public static function reindex(array $array): array
    {
        return static::isIndexedAndFlat($array) ? array_values($array) : $array;
    }

    /**
     * Unset a value from an array of values using a dot-notation path as reference.
     *
     * @param mixed $data
     */
    public static function remove($data, string $path = ''): array
    {
        $data = static::consolidate($data);
        $keys = explode('.', $path);
        $last = array_pop($keys);
        $pointer = &$data;
        foreach ($keys as $key) {
            if (is_array(static::get($pointer, $key))) {
                $pointer = &$pointer[$key];
            }
        }
        unset($pointer[$last]);
        return $data;
    }

    public static function removeEmptyValues(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (Helper::isEmpty($value)) {
                continue;
            }
            $result[$key] = Helper::ifTrue(!is_array($value), $value,
                fn () => static::removeEmptyValues($value)
            );
        }
        return $result;
    }

    public static function restrictKeys(array $array, array $allowedKeys): array
    {
        return array_intersect_key($array, array_fill_keys($allowedKeys, ''));
    }

    /**
     * Search a multidimensional array by key value.
     *
     * @param mixed      $needle
     * @param array      $haystack
     * @param int|string $key
     *
     * @return array|iterable|false
     */
    public static function searchByKey($needle, $haystack, $key)
    {
        if (!is_array($haystack) || array_diff_key($haystack, array_filter($haystack, 'is_iterable'))) {
            return false;
        }
        $index = array_search($needle, wp_list_pluck($haystack, $key));
        if (false !== $index) {
            return $haystack[$index];
        }
        return false;
    }

    /**
     * Set a value to an array of values using a dot-notation path as reference.
     *
     * @param mixed $value
     */
    public static function set(array $data, string $path, $value): array
    {
        $token = strtok($path, '.');
        if (false === $token) {
            return $data; // abort since no path was given
        }
        $ref = &$data;
        while (false !== $token) {
            if (is_object($ref)) {
                $ref = &$ref->$token;
            } else {
                $ref = static::consolidate($ref);
                $ref = &$ref[$token];
            }
            $token = strtok('.');
        }
        $ref = $value;
        return $data;
    }

    public static function unflatten(array $array): array
    {
        $results = [];
        foreach ($array as $path => $value) {
            $results = static::set($results, $path, $value);
        }
        return $results;
    }

    public static function unique(array $values): array
    {
        return Helper::ifTrue(!static::isIndexedAndFlat($values), $values,
            fn () => array_filter(array_unique($values)) // we do not want to reindex the array!
        );
    }

    /**
     * This reindexes the array!
     *
     * @param array|string $values
     */
    public static function uniqueInt($values, bool $absint = true): array
    {
        $values = array_filter(static::convertFromString($values), 'is_numeric');
        $values = array_map('intval', $values);
        if ($absint) {
            $values = array_filter($values, fn ($value) => $value > 0);
        }
        return array_values(array_unique($values));
    }

    public static function unprefixKeys(array $values, string $prefix = '_'): array
    {
        return static::prefixKeys($values, $prefix, false);
    }
}
