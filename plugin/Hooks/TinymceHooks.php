<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\TinymceController;

class TinymceHooks extends AbstractHooks
{
    public function run(): void
    {
        if (!glsr()->filterBool('register/tinymce', true)) {
            return;
        }
        $this->hook(TinymceController::class, [
            ['filterConfig', 'site-reviews/assets/config', 10, 2],
            ['mceShortcodeAjax', 'site-reviews/route/ajax/mce-shortcode'],
            ['registerTinymcePopups', 'admin_init'],
            ['renderTinymceButton', 'media_buttons', 11],
        ]);
    }
}
