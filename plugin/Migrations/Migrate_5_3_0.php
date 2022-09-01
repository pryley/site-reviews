<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\Tables;

class Migrate_5_3_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateDatabase();
        return true;
    }

    protected function fixDatabaseVersion(): void
    {
        $databaseVersion = get_option(glsr()->prefix.'db_version');
        if ('5.2' === $databaseVersion) {
            $version = '1.0'; // @compat
            if (glsr(Tables::class)->columnExists('ratings', 'terms')) {
                $version = Application::DB_VERSION;
            }
            update_option(glsr()->prefix.'db_version', $version);
        }
    }

    protected function migrateDatabase(): void
    {
        require_once ABSPATH.'/wp-admin/includes/plugin.php';
        if (!is_plugin_active_for_network(plugin_basename(glsr()->file))) {
            $this->fixDatabaseVersion();
            return;
        }
        $sites = get_sites([
            'fields' => 'ids',
            'network_id' => get_current_network_id(),
        ]);
        foreach ($sites as $siteId) {
            switch_to_blog($siteId);
            $this->fixDatabaseVersion();
            restore_current_blog();
        }
    }
}
