<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\WelcomeController;

class WelcomeHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(WelcomeController::class, [
            ['filterActionLinks', "plugin_action_links_{$this->basename}", 11],
            ['registerPage', 'admin_menu'],
            ['removeSubMenu', 'admin_init'],
            ['restorePageTitle', 'load-dashboard_page_site-reviews-welcome'],
        ]);
    }
}
