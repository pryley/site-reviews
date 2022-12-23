<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;

class NoticeController extends Controller
{
    /**
     * @return void
     * @admin admin_head
     */
    public function adminNotices()
    {
        $dir = glsr()->path('plugin/Notices');
        if (!is_dir($dir)) {
            return;
        }
        $iterator = new \DirectoryIterator($dir);
        foreach ($iterator as $fileinfo) {
            if (!$fileinfo->isFile()) {
                continue;
            }
            try {
                $notice = '\GeminiLabs\SiteReviews\Notices\\'.$fileinfo->getBasename('.php');
                $reflect = new \ReflectionClass($notice);
                if ($reflect->isInstantiable()) {
                    glsr()->singleton($notice); // make singleton
                    glsr($notice);
                }
            } catch (\ReflectionException $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    /**
     * @return void
     * @action site-reviews/route/admin/dismiss-notice
     */
    public function dismissNotice(Request $request)
    {
        $noticeKey = glsr(Sanitizer::class)->sanitizeText($request->notice);
        $notice = Helper::buildClassName($noticeKey.'-notice', 'Notices');
        if (class_exists($notice)) {
            glsr($notice)->dismiss();
        }
    }

    /**
     * @return void
     * @action site-reviews/route/ajax/dismiss-notice
     */
    public function dismissNoticeAjax(Request $request)
    {
        $this->dismissNotice($request);
        wp_send_json_success();
    }
}
