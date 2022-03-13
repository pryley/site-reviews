<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\SettingsController;

class SettingsHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(SettingsController::class, [
            ['registerSettings', 'admin_init'],
        ]);
    }
}
