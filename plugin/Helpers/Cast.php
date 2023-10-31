<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;

class Cast
{
    /**
     * @param mixed ...$args
     * @return mixed
     */
    public static function to(string $cast = '', ...$args)
    {
        $method = Helper::buildMethodName($cast, 'to');
        if (!empty($cast) && method_exists(__CLASS__, $method)) {
            return call_user_func_array([static::class, $method], $args);
        }
        return array_shift($args);
    }

    /**
     * @param mixed $value
     */
    public static function toArray($value, bool $explode = true): array
    {
        if (is_object($value)) {
            $reflection = new \ReflectionObject($value);
            $properties = $reflection->hasMethod('toArray')
                ? $value->toArray()
                : get_object_vars($value);
            return json_decode(json_encode($properties), true);
        }
        if (is_bool($value)) {
            return [$value];
        }
        if (is_scalar($value) && $explode) {
            return Arr::convertFromString($value);
        }
        return (array) $value;
    }

    /**
     * @param mixed $value
     */
    public static function toBool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $value
     */
    public static function toFloat($value): float
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND);
    }

    /**
     * @param mixed $value
     */
    public static function toInt($value): int
    {
        return (int) round(static::toFloat($value));
    }

    /**
     * @param mixed $value
     * @return object
     */
    public static function toObject($value)
    {
        if (!is_object($value)) {
            return (object) static::toArray($value);
        }
        return $value;
    }

    /**
     * @param mixed $value
     */
    public static function toString($value, $strict = true): string
    {
        if (is_object($value) && in_array('__toString', get_class_methods($value))) {
            return (string) $value->__toString();
        }
        if (Helper::isEmpty($value)) {
            return '';
        }
        if (Arr::isIndexedAndFlat($value)) {
            return implode(', ', $value);
        }
        if (!is_scalar($value)) {
            return $strict ? '' : serialize($value);
        }
        return (string) $value;
    }
}
