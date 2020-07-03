<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Helpers\Url;
use GeminiLabs\Vectorface\Whip\Whip;

class Helper
{
    /**
     * @param string $name
     * @param string $path
     * @return string
     */
    public static function buildClassName($name, $path = '')
    {
        $className = Str::camelCase($name);
        $path = ltrim(str_replace(__NAMESPACE__, '', $path), '\\');
        return !empty($path)
            ? __NAMESPACE__.'\\'.$path.'\\'.$className
            : $className;
    }

    /**
     * @param string $name
     * @param string $prefix
     * @return string
     */
    public static function buildMethodName($name, $prefix = '')
    {
        return lcfirst(Str::camelCase($prefix.'-'.$name));
    }

    /**
     * @param string $name
     * @return string
     */
    public static function buildPropertyName($name)
    {
        return static::buildMethodName($name);
    }

    /**
     * @param string $cast
     * @param mixed $value
     * @return mixed
     */
    public static function castTo($cast = '', $value)
    {
        $method = static::buildMethodName($cast, 'castTo');
        return !empty($cast) && method_exists(__CLASS__, $method)
            ? static::$method($value)
            : $value;
    }

    /**
     * @param mixed $value
     * @return array
     */
    public static function castToArray($value)
    {
        return (array) $value;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function castToBool($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param mixed $value
     * @return float
     */
    public static function castToFloat($value)
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
    }

    /**
     * @param mixed $value
     * @return int
     */
    public static function castToInt($value)
    {
        return (int) filter_var($value, FILTER_VALIDATE_INT);
    }

    /**
     * @param mixed $value
     * @return object
     */
    public static function castToObject($value)
    {
        return (object) (array) $value;
    }

    /**
     * @param mixed $value
     * @return string
     */
    public static function castToString($value)
    {
        if (is_object($value) && in_array('__toString', get_class_methods($value))) {
            return (string) $value->__toString();
        }
        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }
        return (string) $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function filterInput($key, array $request = [])
    {
        if (isset($request[$key])) {
            return $request[$key];
        }
        $variable = filter_input(INPUT_POST, $key);
        if (is_null($variable) && isset($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return $variable;
    }

    /**
     * @param string $key
     * @return array
     */
    public static function filterInputArray($key)
    {
        $variable = filter_input(INPUT_POST, $key, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (empty($variable) && !empty($_POST[$key]) && is_array($_POST[$key])) {
            $variable = $_POST[$key];
        }
        return (array) $variable;
    }

    /**
     * @return string
     */
    public static function getIpAddress()
    {
        $whitelist = [];
        $isUsingCloudflare = !empty(filter_input(INPUT_SERVER, 'CF-Connecting-IP'));
        if (glsr()->filterBool('whip/whitelist/cloudflare', $isUsingCloudflare)) {
            $cloudflareIps = glsr(Cache::class)->getCloudflareIps();
            $whitelist[Whip::CLOUDFLARE_HEADERS] = [Whip::IPV4 => $cloudflareIps['v4']];
            if (defined('AF_INET6')) {
                $whitelist[Whip::CLOUDFLARE_HEADERS][Whip::IPV6] = $cloudflareIps['v6'];
            }
        }
        $whitelist = glsr()->filterArray('whip/whitelist', $whitelist);
        $methods = glsr()->filterInt('whip/methods', Whip::ALL_METHODS);
        $whip = new Whip($methods, $whitelist);
        glsr()->action('whip', $whip);
        if (false !== ($clientAddress = $whip->getValidIpAddress())) {
            return (string) $clientAddress;
        }
        glsr_log()->error('Unable to detect IP address, please see the FAQ page for a possible solution.');
        return 'unknown';
    }

    /**
     * @param string $fromUrl
     * @param int $fallback
     * @return int
     */
    public function getPageNumber($fromUrl = null, $fallback = 1)
    {
        $pagedQueryVar = glsr()->constant('PAGED_QUERY_VAR');
        $pageNum = empty($fromUrl)
            ? filter_input(INPUT_GET, $pagedQueryVar, FILTER_VALIDATE_INT)
            : filter_var(Url::query($fromUrl, $pagedQueryVar), FILTER_VALIDATE_INT);
        if (empty($pageNum)) {
            $pageNum = (int) $fallback;
        }
        return max(1, $pageNum);
    }

    /**
     * @param mixed $value
     * @param mixed $fallback
     * @return bool
     */
    public static function ifEmpty($value, $fallback)
    {
        return static::isEmpty($value) ? $fallback : $value;
    }

    /**
     * @param mixed $value
     * @param string|int $min
     * @param string|int $max
     * @return bool
     */
    public static function inRange($value, $min, $max)
    {
        $inRange = filter_var($value, FILTER_VALIDATE_INT, ['options' => [
            'min_range' => intval($min),
            'max_range' => intval($max),
        ]]);
        return false !== $inRange;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isEmpty($value)
    {
        return !is_numeric($value) && !is_bool($value) && empty($value);
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isGreaterThan($value, $compareWithValue)
    {
        return version_compare($value, $compareWithValue, '>');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isGreaterThanOrEqual($value, $compareWithValue)
    {
        return version_compare($value, $compareWithValue, '>=');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isLessThan($value, $compareWithValue)
    {
        return version_compare($value, $compareWithValue, '<');
    }

    /**
     * @param int|string $value
     * @param int|string $compareWithValue
     * @return bool
     */
    public static function isLessThanOrEqual($value, $compareWithValue)
    {
        return version_compare($value, $compareWithValue, '<=');
    }
}
