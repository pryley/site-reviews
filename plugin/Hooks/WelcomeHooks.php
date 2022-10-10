<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\WelcomeController;

class WelcomeHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(WelcomeController::class, [
            ['filterActionLinks', "plugin_action_links_{$this->basename}", 11],
            ['filterAdminTitle', 'admin_title'],
            ['registerPage', 'admin_menu'],
            ['removeSubMenu', 'admin_init'],
        ]);
    }
}
