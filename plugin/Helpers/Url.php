<?php

namespace GeminiLabs\SiteReviews\Helpers;

class Url
{
    /**
     * @param string $path
     * @return string
     */
    public static function home($path = '')
    {
        return trailingslashit(network_home_url($path));
    }

    /**
     * @param string $url
     * @return string
     */
    public static function path($url)
    {
        return untrailingslashit(parse_url($url, PHP_URL_PATH));
    }

    /**
     * @param string $url
     * @return array
     */
    public static function queries($url)
    {
        $queries = [];
        $str = (string) parse_url((string) $url, PHP_URL_QUERY);
        parse_str($str, $queries);
        return $queries;
    }

    /**
     * @param string $url
     * @param string $param
     * @param string|int $fallback
     * @return string
     */
    public static function query($url, $param, $fallback = null)
    {
        return Arr::get(static::queries($url), $param, $fallback);
    }
}
