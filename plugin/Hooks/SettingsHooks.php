<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\SettingsController;

class SettingsHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(SettingsController::class, [
            ['registerSettings', 'admin_init'],
        ]);
    }
}
