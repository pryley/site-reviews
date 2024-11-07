<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Modules\Notice;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->unsupportedVersionNotice();
            return;
        }
        $this->hook(Controller::class, [
            ['printInlineScripts', 'ux_builder_enqueue_scripts'],
            ['printInlineStyles', 'ux_builder_enqueue_scripts'],
            ['registerShortcodes', 'init'],
            ['searchAssignedPosts', 'wp_ajax_ux_builder_search_posts', 1],
            ['searchAssignedUsers', 'wp_ajax_ux_builder_search_posts', 2],
        ]);
    }

    protected function isInstalled(): bool
    {
        return 'flatsome' === wp_get_theme(get_template())->get('TextDomain');
    }

    protected function isVersionSupported(): bool
    {
        return version_compare(wp_get_theme(get_template())->get('Version'), '3.19.0', '>=');
    }

    protected function unsupportedVersionNotice(): void
    {
        add_action('admin_notices', function () {
            if (!str_starts_with(glsr_current_screen()->post_type, glsr()->post_type)) {
                return;
            }
            glsr(Notice::class)->addWarning(
                _x('Update the Flatsome theme to v3.19.0 or higher to enable integration with Site Reviews.', 'admin-text', 'site-reviews')
            );
        });
    }
}
