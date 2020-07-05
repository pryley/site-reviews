<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\SiteReviews\Helper;

class Cast
{
    /**
     * @param string $cast
     * @param mixed $value
     * @return mixed
     */
    public static function to($cast = '', $value)
    {
        $method = Helper::buildMethodName($cast, 'to');
        return !empty($cast) && method_exists(__CLASS__, $method)
            ? static::$method($value)
            : $value;
    }

    /**
     * @param mixed $value
     * @return array
     */
    public static function toArray($value)
    {
        return (array) $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function toBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $value
     * @return float
     */
    public static function toFloat($value)
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
    }

    /**
     * @param mixed $value
     * @return int
     */
    public static function toInt($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param mixed $value
     * @return object
     */
    public static function toObject($value)
    {
        return (object) (array) $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function toString($value)
    {
        if (is_object($value) && in_array('__toString', get_class_methods($value))) {
            return (string) $value->__toString();
        }
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string) $value;
    }
}
