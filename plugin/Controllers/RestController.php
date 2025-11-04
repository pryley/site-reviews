<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestShortcodeController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSummaryController;

class RestController
{
    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestShortcodeController())->register_routes();
        (new RestSummaryController())->register_routes();
    }
}
