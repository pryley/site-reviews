<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\HookProxy;

class RestController implements ControllerContract
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
