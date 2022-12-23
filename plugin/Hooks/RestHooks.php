<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestController;

class RestHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(RestController::class, [
            ['registerRoutes', 'rest_api_init'],
        ]);
    }
}
