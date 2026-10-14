<?php

namespace GeminiLabs\SiteReviews;

class Rollback
{
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
