<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

/**
 * Collects the deprecation notices and writes them to the console.
 */
class DeprecationController extends AbstractController
{
    /**
     * @action admin_footer
     * @action wp_footer
     */
    public function logNotices(): void
    {
        $notices = glsr()->retrieveAs('array', 'deprecated', []);
        $notices = array_keys(array_flip(array_filter($notices)));
        natsort($notices);
        array_walk($notices, fn ($notice) => glsr_log()->notice($notice));
    }

    /**
     * @param mixed $function
     * @param mixed $replacement
     * @param mixed $version
     *
     * @action deprecated_function_run
     */
    public function storeFunctionNotice($function, $replacement, $version): void
    {
        if (!str_starts_with($function, glsr()->prefix)) {
            return;
        }
        if (empty($replacement)) {
            $notice = sprintf(
                'Function %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
                $function,
                $version
            );
        } else {
            $notice = sprintf(
                'Function %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
                $function,
                $version,
                $replacement
            );
        }
        glsr()->append('deprecated', $notice);
    }

    /**
     * @param mixed $hook
     * @param mixed $replacement
     * @param mixed $version
     * @param mixed $message
     *
     * @action deprecated_hook_run
     */
    public function storeHookNotice($hook, $replacement, $version, $message): void
    {
        if (!str_starts_with($hook, glsr()->id)) {
            return;
        }
        if (empty($replacement)) {
            $notice = sprintf(
                'Hook %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.',
                $hook,
                $version
            );
        } else {
            $notice = sprintf(
                'Hook %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.',
                $hook,
                $version,
                $replacement
            );
        }
        glsr()->append('deprecated', trim($notice.' '.$message));
    }
}
