<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class Install
{
    public function deactivate(bool $isNetworkDeactivation): void
    {
        if (!$isNetworkDeactivation) {
            glsr(Tables::class)->dropForeignConstraints();
            delete_option(glsr()->prefix.'activated');
            glsr()->action('deactivated');
            return;
        }
        foreach ($this->sites() as $siteId) {
            switch_to_blog($siteId);
            glsr(Tables::class)->dropForeignConstraints();
            delete_option(glsr()->prefix.'activated');
            glsr()->action('deactivated');
            restore_current_blog();
        }
    }

    public function dropTables(bool $dropAll = true): void
    {
        $tables = $this->tables();
        if (is_multisite() && $dropAll) {
            foreach ($this->sites() as $siteId) {
                switch_to_blog($siteId);
                $tables = array_unique(array_merge($tables, $this->tables()));
                delete_option(glsr()->prefix.'db_version');
                restore_current_blog();
            }
        }
        foreach ($tables as $table) {
            glsr(Database::class)->dbQuery(
                glsr(Query::class)->sql("DROP TABLE IF EXISTS {$table}")
            );
        }
        delete_option(glsr()->prefix.'db_version');
    }

    public function run(): void
    {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        if (is_plugin_active_for_network(glsr()->basename)) {
            foreach ($this->sites() as $siteId) {
                $this->runOnSite((int) $siteId);
            }
            return;
        }
        $this->install();
    }

    public function runOnSite(int $siteId): void
    {
        switch_to_blog($siteId);
        $this->install();
        restore_current_blog();
    }

    public function sites(): array
    {
        return (array) get_sites([
            'count' => false, // this ensures we return an array
            'fields' => 'ids',
            'network_id' => get_current_network_id(),
        ]);
    }

    protected function install(): void
    {
        glsr(Role::class)->resetAll();
        glsr(Tables::class)->createTables();
        glsr(Tables::class)->addForeignConstraints();
        if (glsr(Tables::class)->tablesExist() && empty(get_option(glsr()->prefix.'db_version'))) {
            $version = '1.0'; // @compat
            if (glsr(Tables::class)->columnExists('ratings', 'terms')) {
                $version = '1.1';
            }
            if (glsr(Tables::class)->columnExists('ratings', 'score')) {
                $version = Application::DB_VERSION;
            }
            add_option(glsr()->prefix.'db_version', $version);
        }
    }

    protected function tables(): array
    {
        $tables = [];
        foreach (glsr(Tables::class)->tables() as $table) {
            $tables[] = glsr($table)->tablename;
        }
        return $tables;
    }
}
