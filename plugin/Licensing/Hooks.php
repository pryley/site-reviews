<?php

namespace GeminiLabs\SiteReviews\Licensing;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Licensing\Controllers\Controller;
use GeminiLabs\SiteReviews\Licensing\Controllers\RestController;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterSubmenuPages', 'site-reviews/addon/submenu/pages'],
            ['noticeInstalled', "load-{$this->type}_page_glsr-settings"],
            ['sanitizeLicenses', 'site-reviews/settings/sanitize', 10, 2],
        ]);
        $this->hook(RestController::class, [
            ['registerRoutes', 'rest_api_init'],
        ]);
    }
}
