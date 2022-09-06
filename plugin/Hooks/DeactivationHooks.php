<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\DeactivationController;
use GeminiLabs\SiteReviews\Helper;

class DeactivationHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        if (Helper::isLocalServer()) {
            return;
        }
        $this->hook(DeactivationController::class, [
            ['enqueueAssets', 'admin_enqueue_scripts'],
            ['filterActionLinks', 'plugin_action_links_'.$this->basename],
            ['renderTemplate', 'admin_footer'],
            ['submitDeactivateReasonAjax', 'site-reviews/route/ajax/deactivate'],
        ]);
    }
}
