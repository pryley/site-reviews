<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\DashboardController;

class DashboardHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(DashboardController::class, [
            ['flushMonthlyCountCache', 'transition_post_status', 5, 3],
            ['registerMetaBoxes', 'wp_dashboard_setup'],
        ]);
    }
}
