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
        return untrailingslashit((string) wp_parse_url($url, PHP_URL_PATH));
    }

    public static function queries(?string $url): array
    {
        $queries = [];
        $str = (string) wp_parse_url((string) $url, PHP_URL_QUERY);
        parse_str($str, $queries);
        return $queries;
    }

    /**
     * @param string|int|null $fallback
     *
     * @return mixed
     */
    public static function query(string $url, string $param, $fallback = null)
    {
        return Arr::get(static::queries($url), $param, $fallback);
    }
}
