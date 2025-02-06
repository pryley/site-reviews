<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TinymceController;

class TinymceHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(TinymceController::class, [
            ['filterAdminVariables', 'site-reviews/enqueue/admin/localize'],
            ['mceShortcodeAjax', 'site-reviews/route/ajax/mce-shortcode'],
            ['registerTinymcePopups', 'admin_init'],
            ['renderTinymceButton', 'media_buttons', 11],
        ]);
    }
}
