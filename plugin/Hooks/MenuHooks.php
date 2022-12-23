<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\MenuController;

class MenuHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(MenuController::class, [
            ['registerMenuCount', 'admin_menu'],
            ['registerSubMenus', 'admin_menu'],
            ['removeSubMenu', 'admin_init'],
            ['setCustomPermissions', 'admin_init', 999],
        ]);
    }
}
