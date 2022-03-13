<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\MenuController;

class MenuHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(MenuController::class, [
            ['registerMenuCount', 'admin_menu'],
            ['registerSubMenus', 'admin_menu'],
            ['removeSubMenus', 'admin_menu', 20],
            ['setCustomPermissions', 'admin_init', 999],
        ]);
    }
}
