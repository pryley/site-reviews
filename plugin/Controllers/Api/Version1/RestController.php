<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

class RestController
{
    /**
     * @return void
     * @action rest_api_init
     */
    public function registerRoutes()
    {
        (new RestSummaryController())->register_routes();
        (new RestTypeController())->register_routes();
    }
}
