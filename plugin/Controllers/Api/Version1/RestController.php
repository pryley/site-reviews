<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

class RestController
{
    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestSummaryController())->register_routes();
        (new RestTypeController())->register_routes();
    }
}
