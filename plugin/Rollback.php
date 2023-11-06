<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Overrides\PluginUpgrader;

class Rollback
{
    public function rollback(string $version): void
    {
        global $title, $parent_file;
        $plugin = glsr()->basename;
        $parent_file = 'edit.php?post_type='.glsr()->post_type;
        $title = _x('Rollback Site Reviews', 'admin-text', 'site-reviews');
        $nonce = 'upgrade-plugin_'.$plugin;
        $url = 'update.php?action=upgrade-plugin&plugin='.urlencode($plugin);
        wp_enqueue_script('updates');
        require_once ABSPATH.'wp-admin/admin-header.php';
        $upgrader = new PluginUpgrader(
            new \Plugin_Upgrader_Skin(compact('title', 'nonce', 'url', 'plugin'))
        );
        $upgrader->rollback($version);
        require_once ABSPATH.'wp-admin/admin-footer.php';
    }

    public function rollbackData(string $version): array
    {
        set_transient(glsr()->prefix.'rollback_version', $version, MINUTE_IN_SECONDS);
        return [
            'data' => [
                '_ajax_nonce' => wp_create_nonce('updates'),
                'action' => 'update-plugin',
                'plugin' => glsr()->basename,
                'slug' => glsr()->id,
            ],
            'url' => glsr_admin_url('welcome'),
        ];
    }
}
