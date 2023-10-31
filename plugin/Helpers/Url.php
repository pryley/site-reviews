<?php

namespace GeminiLabs\SiteReviews\Helpers;

class Url
{
    public static function home(string $path = ''): string
    {
        return trailingslashit(network_home_url($path));
    }

    public static function path(string $url): string
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
