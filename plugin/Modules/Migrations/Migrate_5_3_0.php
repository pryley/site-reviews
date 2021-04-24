<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Application;

class Migrate_5_3_0
{
    /**
     * @return bool
     */
    public function run()
    {
        $this->migrateDatabase();
        return true;
    }

    /**
     * @return void
     */
    protected function fixDatabaseVersion()
    {
        $databaseVersion = get_option(glsr()->prefix.'db_version');
        if ('5.2' === $databaseVersion) {
            update_option(glsr()->prefix.'db_version', '1.0');
        }
    }

    /**
     * @return void
     */
    protected function migrateDatabase()
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
