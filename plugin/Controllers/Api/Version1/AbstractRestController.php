<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

/**
 * Base class for the public frontend routes. It does not extend WP_REST_Controller:
 * these routes return rendered command results, so none of the resource machinery
 * (context/field filtering, collection params) applies.
 */
abstract class AbstractRestController
{
    abstract public function registerRoutes(): void;

    protected function respond(array $data, int $status = 200): \WP_REST_Response
    {
        return new \WP_REST_Response($data, $status);
    }

    protected function restNamespace(): string
    {
        return glsr()->id.'/v1';
    }
}
