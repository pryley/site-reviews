<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Router;

class RouterHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Router::class, [
            ['routeAdminAjaxRequest', "wp_ajax_{$this->prefix}admin_action"],
            ['routeAdminGetRequest', 'admin_init'],
            ['routeAdminPostRequest', 'admin_init'],
            ['routePublicAjaxRequest', "wp_ajax_{$this->prefix}public_action"],
            ['routePublicAjaxRequest', "wp_ajax_nopriv_{$this->prefix}public_action"],
            ['routePublicGetRequest', 'parse_request'],
            ['routePublicPostRequest', 'init'],
        ]);
    }
}
