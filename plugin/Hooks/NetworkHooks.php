<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\NetworkController;

class NetworkHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(NetworkController::class, [
            ['extendAdminBar', 'admin_bar_menu', 30],
        ]);
    }
}
