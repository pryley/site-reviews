<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

/**
 * Base class for the plugin's own REST routes. It does not extend WP_REST_Controller:
 * these routes return command and query results directly, so the resource machinery
 * (context/field filtering, additional fields) does not apply. The reviews CRUD
 * controller stays on WP_REST_Controller because core's rest_controller_class
 * accepts only that subclass.
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
