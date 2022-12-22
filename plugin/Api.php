<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helper;

class Api
{
    protected const BASE_URL = 'https://api.site-reviews.com/v1/';

    public function args(array $args = []): array
    {
        return wp_parse_args($args, [
            'sslverify' => Helper::isLocalServer(),
        ]);
     }

    public function get(string $path, array $args = []): Response
    {
        $args['method'] = 'GET';
        return $this->request($path, $args);
    }

    public function post(string $path, array $args = []): Response
    {
        $args['method'] = 'POST';
        return $this->request($path, $args);
    }

    public function request(string $path, array $args = []): Response
    {
        $transient = sprintf('%sapi_%s', glsr()->prefix, sanitize_key($path));
        $response = get_transient($transient);
        if (!empty($response) && empty($args['force'])) { // bypass transient with: $arg['force'] = true
            return new Response($response);
        }
        $response = wp_remote_request($this->url($path), $this->args($args));
        if (!is_wp_error($response)) {
            set_transient($transient, $response, DAY_IN_SECONDS);
        }
        return new Response($response);
    }

    public function url(string $path): string
    {
        $baseUrl = glsr()->filterString('api/base_url', static::BASE_URL);
        return trailingslashit(static::BASE_URL).ltrim($path, '/');
    }
}
