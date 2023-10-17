<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\FlyoutController;

class FlyoutHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(FlyoutController::class, [
            ['renderFlyout', 'admin_footer'],
        ]);
    }
}
