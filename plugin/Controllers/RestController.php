<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestRenderController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestShortcodeController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSubmissionController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSummaryController;

class RestController
{
    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestRenderController())->registerRoutes();
        (new RestShortcodeController())->registerRoutes();
        (new RestSubmissionController())->registerRoutes();
        (new RestSummaryController())->registerRoutes();
    }
}
