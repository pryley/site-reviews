<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateDatabase;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateReviews;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateSettings;
use GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0\MigrateSidebars;
use GeminiLabs\SiteReviews\Role;

class Migrate_5_25_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        glsr(MigrateSettings::class)->run(); // 1
        glsr(MigrateSidebars::class)->run(); // 2
        glsr(MigrateReviews::class)->run();  // 3
        glsr(MigrateDatabase::class)->run(); // 4
        $this->migratePermissions();
        $this->cleanup();
        return true;
    }

    protected function cleanup(): void
    {
        wp_clear_scheduled_hook('site-reviews/schedule/session/purge');
        delete_option('glsr_trustalyze');
        delete_option('_glsr_trustalyze');
        delete_option('widget_site-reviews');
        delete_option('widget_site-reviews-form');
        delete_option('widget_site-reviews-summary');
        delete_option(glsr()->id.'activated');
        delete_transient(glsr()->id.'_cloudflare_ips');
        delete_transient(glsr()->id.'_remote_post_test');
        delete_transient(glsr()->prefix.'system_info');
    }

    protected function migratePermissions(): void
    {
        $roles = glsr(Role::class)->roles();
        $newCapabilities = ['create_posts', 'respond_to_posts', 'respond_to_others_posts'];
        foreach ($roles as $role => $capabilities) {
            foreach ($newCapabilities as $capability) {
                if (!in_array($capability, $capabilities)) {
                    continue;
                }
                $wpRole = get_role($role);
                if (!empty($wpRole)) {
                    $wpRole->add_cap(glsr(Role::class)->capability($capability));
                }
            }
        }
    }
}
