<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;

class Arr
{
    /**
     * @return bool
     */
    public static function compare(array $arr1, array $arr2)
    {
        sort($arr1);
        sort($arr2);
        return $arr1 == $arr2;
    }

    /**
     * Returns an empty array if value is scalar
     * @param mixed $array
     * @return array
     */
    public static function consolidate($array)
    {
        if (is_object($array)) {
            $values = get_object_vars($array);
            $array = empty($values) ? (array) $array : $values;
        }
        return is_array($array) ? $array : [];
    }

    /**
     * @return array
     */
    public static function convertFromDotNotation(array $array)
    {
        $results = [];
        foreach ($array as $path => $value) {
            $results = static::set($results, $path, $value);
        }
        return $results;
    }

    /**
     * @param mixed $value
     * @param mixed $callback
     * @return array
     */
    public static function convertFromString($value, $callback = null)
    {
        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value));
        }
        if (is_numeric($value)) {
            $value = (array) $value;
        }
        return $callback
            ? array_filter((array) $value, $callback)
            : array_filter((array) $value, Helper::class.'::isNotEmpty');
    }

    /**
     * @param bool $flattenValue
     * @param string $prefix
     * @return array
     */
    public static function flatten(array $array, $flattenValue = false, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = ltrim($prefix.'.'.$key, '.');
            if (static::isIndexedAndFlat($value)) {
                if ($flattenValue) {
                    $value = '['.implode(', ', $value).']';
                }
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
     * @param mixed $data
     * @param string $path
     * @param mixed $fallback
     * @return mixed
     */
    public static function get($data, $path = '', $fallback = '')
    {
        $data = static::consolidate($data);
        $keys = explode('.', $path);
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
     * @param string $key
     * @return array
     */
    public static function insertAfter($key, array $array, array $insert)
    {
        return static::insert($array, $insert, $key, 'after');
    }

    /**
     * @param string $key
     * @return array
     */
    public static function insertBefore($key, array $array, array $insert)
    {
        return static::insert($array, $insert, $key, 'before');
    }

    /**
     * @param string $key
     * @param string $position
     * @return array
     */
    public static function insert(array $array, array $insert, $key, $position = 'before')
    {
        $keyPosition = intval(array_search($key, array_keys($array)));
        if ('after' == $position) {
            ++$keyPosition;
        }
        if (false !== $keyPosition) {
            $result = array_slice($array, 0, $keyPosition);
            $result = array_merge($result, $insert);
            return array_merge($result, array_slice($array, $keyPosition));
        }
        return array_merge($array, $insert);
    }

    /**
     * @param mixed $array
     * @return bool
     */
    public static function isIndexedAndFlat($array)
    {
        if (!is_array($array) || array_filter($array, 'is_array')) {
            return false;
        }
        return wp_is_numeric_array($array);
    }

    /**
     * @param bool $prefixed
     * @return array
     */
    public static function prefixKeys(array $values, $prefixed = true)
    {
        $trim = '_';
        $prefix = $prefixed
            ? $trim
            : '';
        $prefixed = [];
        foreach ($values as $key => $value) {
            $key = trim($key);
            if (0 === strpos($key, $trim)) {
                $key = substr($key, strlen($trim));
            }
            $prefixed[$prefix.$key] = $value;
        }
        return $prefixed;
    }

    /**
     * @param array $array
     * @param mixed $value
     * @param mixed $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (!is_null($key)) {
            return [$key => $value] + $array;
        }
        array_unshift($array, $value);
        return $array;
    }

    /**
     * @param mixed $array
     * @return array
     */
    public static function reindex($array)
    {
        return static::isIndexedAndFlat($array)
            ? array_values($array)
            : $array;
    }

    /**
     * @return array
     */
    public static function removeEmptyValues(array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (Helper::isEmpty($value)) {
                continue;
            }
            $result[$key] = is_array($value)
                ? static::removeEmptyValues($value)
                : $value;
        }
        return $result;
    }

    /**
     * Set a value to an array of values using a dot-notation path as reference.
     * @param mixed $data
     * @param string $path
     * @param mixed $value
     * @return array
     */
    public static function set($data, $path, $value)
    {
        $token = strtok($path, '.');
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

    /**
     * @return array
     */
    public static function unique(array $values)
    {
        return static::isIndexedAndFlat($values)
            ? array_values(array_filter(array_unique($values)))
            : $values;
    }

    /**
     * @param array|string $values
     * @return array
     */
    public static function uniqueInt($values)
    {
        return static::unique(array_map('absint', static::convertFromString($values)));
    }

    /**
     * @return array
     */
    public static function unprefixKeys(array $values)
    {
        return static::prefixKeys($values, false);
    }
}
