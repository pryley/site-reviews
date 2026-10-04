<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\RestController;

class RestHooks extends AbstractHooks
{
    public function run(): void
    {
        if (wp_installing()) { // because rest_preload_api_request runs during woo updates
            return;
        }
        $this->hook(RestController::class, [
            ['dispatchAjaxRequest', "wp_ajax_{$this->prefix}rest_request"],
            ['dispatchAjaxRequest', "wp_ajax_nopriv_{$this->prefix}rest_request"],
            ['registerRoutes', 'rest_api_init'],
        ]);
    }
}
