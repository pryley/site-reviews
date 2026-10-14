<?php

namespace GeminiLabs\SiteReviews;

class Rollback
{
    /**
     * Check that this is the update-plugin ajax request of WordPress for this plugin.
     */
    public function isUpdateRequest(): bool
    {
        $plugin = plugin_basename(sanitize_text_field(wp_unslash($_POST['plugin'] ?? '')));
        return wp_doing_ajax()
            && 'update-plugin' === ($_POST['action'] ?? '')
            && glsr()->basename === $plugin;
    }

    public function rollbackData(string $version): array
    {
        set_transient(glsr()->prefix.'rollback_version', $version, \MINUTE_IN_SECONDS);
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
