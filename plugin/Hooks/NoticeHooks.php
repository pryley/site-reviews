<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\NoticeController;

class NoticeHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(NoticeController::class, [
            ['adminNotices', 'admin_notices'],
            ['dismissNotice', 'site-reviews/route/admin/dismiss-notice'],
            ['dismissNoticeAjax', 'site-reviews/route/ajax/dismiss-notice'],
        ]);
    }
}
