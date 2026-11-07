<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Notices\AbstractNotice;

class NoticeController extends AbstractController
{
    /**
     * @action current_screen
     */
    public function activatePlugin(): void
    {
        $action = filter_input(\INPUT_GET, 'action');
        $plugin = filter_input(\INPUT_GET, 'plugin');
        $trigger = filter_input(\INPUT_GET, 'trigger');
        if ('activate' !== $action || 'notice' !== $trigger || empty($plugin)) {
            return;
        }
        check_admin_referer("activate-plugin_{$plugin}");
        $result = activate_plugin($plugin, '', is_network_admin(), true);
        if (is_wp_error($result)) {
            wp_die($result->get_error_message());
        }
        wp_safe_redirect(wp_get_referer());
        glsr_exit();
    }

    /**
     * @admin admin_head
     */
    public function adminNotices(): void
    {
        foreach ($this->noticeClasses() as $notice) {
            glsr()->singleton($notice);
            glsr($notice);
        }
    }

    /**
     * Close the hidden div used to prevent notices from flickering before
     * they are moved elsewhere in the page by WordPress Core.
     *
     * @action admin_notices
     */
    public function injectAfterNotices(): void
    {
        if (str_contains(glsr_current_screen()->id, glsr()->post_type)) {
            echo '</div>';
        }
    }

    /**
     * Wrap the notices in a hidden div to prevent flickering before
     * they are moved elsewhere in the page by WordPress Core.
     *
     * @action admin_notices
     */
    public function injectBeforeNotices(): void
    {
        if (str_contains(glsr_current_screen()->id, glsr()->post_type)) {
            echo '<div id="glsr-notice-catcher">';
        }
    }

    /**
     * The notice classes of the plugin and addons.
     *
     * @return class-string<AbstractNotice>[]
     */
    public function noticeClasses(): array
    {
        $notices = [];
        try {
            $iterator = new \DirectoryIterator(glsr()->path('plugin/Notices'));
            foreach ($iterator as $fileinfo) {
                if (!$fileinfo->isFile()) {
                    continue;
                }
                $notice = 'GeminiLabs\SiteReviews\Notices\\'.$fileinfo->getBasename('.php');
                $reflection = new \ReflectionClass($notice);
                if ($reflection->isInstantiable()) {
                    $notices[] = $reflection->getName();
                }
            }
        } catch (\Throwable $e) {
            glsr_log()->error($e->getMessage());
        }
        return glsr()->filterArray('notices', $notices);
    }
}
