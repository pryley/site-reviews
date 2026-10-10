<?php

namespace GeminiLabs\SiteReviews\Helpers;

class Url
{
    public static function home(string $path = ''): string
    {
        return trailingslashit(network_home_url($path));
    }

    /**
     * The URL a licence is activated for: the network's, unless the site has a mapped domain.
     */
    public static function license(): string
    {
        if (!is_multisite()) {
            return static::home();
        }
        $network = strtolower((string) preg_replace('|^www\.|', '', (string) get_network()->domain));
        $site = strtolower((string) get_site()->domain);
        if ($site === $network || str_ends_with($site, '.'.$network)) {
            return static::home();
        }
        return trailingslashit(home_url());
    }

    public static function path(string $url): string
    {
        return untrailingslashit((string) wp_parse_url($url, \PHP_URL_PATH));
    }

    public static function queries(?string $url): array
    {
        $queries = [];
        $str = (string) wp_parse_url((string) $url, \PHP_URL_QUERY);
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
