<?php

namespace GeminiLabs\SiteReviews\Modules;

/**
 * The request block of GLSR.config needed by the Request client and inline scripts.
 */
class RequestConfig
{
    public function inlineScript(): string
    {
        return sprintf('window.GLSR = window.GLSR || {}; window.GLSR.config = window.GLSR.config || %s;', wp_json_encode([
            'nameprefix' => glsr()->id,
            'request' => $this->toArray(),
        ], \JSON_UNESCAPED_SLASHES));
    }

    public function toArray(array $routes = []): array
    {
        return [
            'ajax' => [
                'rest' => glsr()->prefix.'rest_request',
                'url' => admin_url('admin-ajax.php'),
            ],
            'nonce' => is_user_logged_in() ? wp_create_nonce('wp_rest') : false,
            'routes' => $routes,
            'url' => esc_url_raw(rest_url(glsr()->id.'/v1/')),
        ];
    }
}
