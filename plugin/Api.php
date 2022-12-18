<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helper;

class Api
{
    // protected const BASE_URL = 'https://api.site-reviews.com/v1/';
    protected const BASE_URL = 'http://site-reviews-api.test/v1/';

    public function args(array $args = []): array
    {
        return wp_parse_args($args, [
            'sslverify' => Helper::isLocalServer(),
        ]);
     }

    public function get(string $path, array $args = []): Response
    {
        return new Response(
            wp_remote_get($this->url($path), $this->args($args))
        );
    }

    public function post(string $path, array $args = []): Response
    {
        return new Response(
            wp_remote_post($this->url($path), $this->args($args))
        );
    }

    public function url(string $path): string
    {
        return trailingslashit(static::BASE_URL).ltrim($path, '/');
    }
}
