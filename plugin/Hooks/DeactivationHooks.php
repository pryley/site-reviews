<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\DeactivationController;

class DeactivationHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(DeactivationController::class, [
            ['enqueueAssets', 'admin_enqueue_scripts'],
            ['filterActionLinks', "plugin_action_links_{$this->basename}"],
            ['renderTemplate', 'admin_footer'],
            ['submitDeactivateReasonAjax', 'site-reviews/route/ajax/deactivate'],
        ]);
    }
}
