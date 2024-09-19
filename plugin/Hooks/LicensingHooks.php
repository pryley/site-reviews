<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\LicensingController;

class LicensingHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(LicensingController::class, [
            ['sanitizeLicenses', 'site-reviews/settings/sanitize', 10, 2],
        ]);
    }
}
