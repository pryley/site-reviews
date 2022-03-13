<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\PrivacyController;

class PrivacyHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(PrivacyController::class, [
            ['filterPersonalDataErasers', 'wp_privacy_personal_data_erasers'],
            ['filterPersonalDataExporters', 'wp_privacy_personal_data_exporters'],
            ['privacyPolicyContent', 'admin_init'],
        ]);
    }
}
