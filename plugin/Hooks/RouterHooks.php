<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Router;

class RouterHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(Router::class, [
            ['routeAdminAjaxRequest', "wp_ajax_{$this->prefix}action"],
            ['routeAdminPostRequest', 'admin_init'],
            ['routePublicAjaxRequest', "wp_ajax_nopriv_{$this->prefix}action"],
            ['routePublicPostRequest', 'init'],
        ]);
    }
}
