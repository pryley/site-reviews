<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\NoticeController;

class NoticeHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(NoticeController::class, [
            ['activatePlugin', 'current_screen'],
            ['adminNotices', 'admin_head'],
            ['dismissNotice', 'site-reviews/route/admin/dismiss-notice'],
            ['dismissNoticeAjax', 'site-reviews/route/ajax/dismiss-notice'],
            ['injectAfterNotices', 'admin_notices', PHP_INT_MAX],
            ['injectBeforeNotices', 'admin_notices', -9999],
        ]);
    }
}
