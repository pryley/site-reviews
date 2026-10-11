<?php

namespace GeminiLabs\SiteReviews\Connect;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['filterSubmenuPages', 'site-reviews/addon/submenu/pages'],
            ['noticeInstalled', "load-{$this->type}_page_glsr-settings"],
        ]);
        $this->hook(RestController::class, [
            ['registerRoutes', 'rest_api_init'],
        ]);
    }
}
