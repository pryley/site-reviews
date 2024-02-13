<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\HookProxy;

class RestController
{
    use HookProxy;

    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestSummaryController())->register_routes();
        (new RestTypeController())->register_routes();
    }
}
