<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;

class NoticeController extends AbstractController
{
    /**
     * @action current_screen
     */
    public function activatePlugin(): void
    {
        $action = filter_input(INPUT_GET, 'action');
        $plugin = filter_input(INPUT_GET, 'plugin');
        $trigger = filter_input(INPUT_GET, 'trigger');
        if ('activate' !== $action || 'notice' !== $trigger || empty($plugin)) {
            return;
        }
        check_admin_referer("activate-plugin_{$plugin}");
        $result = activate_plugin($plugin, '', is_network_admin(), true);
        if (is_wp_error($result)) {
            wp_die($result->get_error_message());
        }
        wp_safe_redirect(wp_get_referer());
        exit;
    }

    /**
     * @admin admin_head
     */
    public function adminNotices(): void
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
     * @action site-reviews/route/admin/dismiss-notice
     */
    public function dismissNotice(Request $request): void
    {
        $noticeKey = glsr(Sanitizer::class)->sanitizeText($request->notice);
        $notice = Helper::buildClassName($noticeKey.'-notice', 'Notices');
        if (class_exists($notice)) {
            glsr($notice)->dismiss();
        }
    }

    /**
     * @action site-reviews/route/ajax/dismiss-notice
     */
    public function dismissNoticeAjax(Request $request): void
    {
        $this->dismissNotice($request);
        wp_send_json_success();
    }

    /**
     * @action admin_notices
     */
    public function injectAfterNotices(): void
    {
        if (str_contains(glsr_current_screen()->id, glsr()->post_type)) {
            // Close the hidden div used to prevent notices from flickering before
            // they are moved elsewhere in the page by WordPress Core.
            echo '</div>';
        }
    }

    /**
     * @action admin_notices
     */
    public function injectBeforeNotices(): void
    {
        if (str_contains(glsr_current_screen()->id, glsr()->post_type)) {
            // Wrap the notices in a hidden div to prevent flickering before
            // they are moved elsewhere in the page by WordPress Core.
            echo '<div id="glsr-notice-catcher">';
        }
    }
}
