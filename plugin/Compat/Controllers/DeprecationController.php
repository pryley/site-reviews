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
     * @action deprecated_function_run
     */
    public function storeFunctionNotice(string $function, string $replacement, string $version): void
    {
        if (!str_starts_with($function, glsr()->prefix)) {
            return;
        }
        $format = '' === $replacement
            ? 'Function %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'
            : 'Function %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.';
        glsr()->append('deprecated', sprintf($format, $function, $version, $replacement));
    }

    /**
     * @action deprecated_hook_run
     */
    public function storeHookNotice(string $hook, string $replacement, string $version, string $message): void
    {
        if (!str_starts_with($hook, glsr()->id)) {
            return;
        }
        $format = '' === $replacement
            ? 'Hook %1$s is <strong>deprecated</strong> since version %2$s with no alternative available.'
            : 'Hook %1$s is <strong>deprecated</strong> since version %2$s! Use %3$s instead.';
        $notice = sprintf($format, $hook, $version, $replacement);
        glsr()->append('deprecated', trim($notice.' '.$message));
    }
}
