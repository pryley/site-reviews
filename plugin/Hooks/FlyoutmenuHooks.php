<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\FlyoutmenuController;

class FlyoutmenuHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(FlyoutmenuController::class, [
            ['renderFlyoutmenu', 'admin_footer'],
        ]);
    }
}
