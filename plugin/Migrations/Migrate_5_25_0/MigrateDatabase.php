<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_5_25_0;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableRatings;
use GeminiLabs\SiteReviews\Install;

class MigrateDatabase implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->repairDatabase();
        $this->migrateDatabase();
        glsr(TableRatings::class)->removeInvalidRows();
        glsr(CountManager::class)->recalculate();
        return true;
    }

    protected function install(): void
    {
        glsr(Tables::class)->createTables();
        glsr(Tables::class)->addForeignConstraints();
    }

    protected function isDatabaseVersionUpdated(): bool
    {
        if (glsr(Tables::class)->columnExists('ratings', 'terms')) {
            if (version_compare(glsr(Database::class)->version(), '1.1', '<')) {
                update_option(glsr()->prefix.'db_version', '1.1');
            }
            return true;
        }
        return false;
    }

    protected function migrateDatabase(): bool
    {
        if ($this->isDatabaseVersionUpdated()) {
            return true;
        }
        if (glsr(Tables::class)->isSqlite()) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD COLUMN terms tinyint(1) NOT NULL DEFAULT '1'
            ");
        } else {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD COLUMN terms tinyint(1) NOT NULL DEFAULT '1'
                AFTER url
            ");
        }
        glsr(Database::class)->dbQuery($sql);
        if ($this->isDatabaseVersionUpdated()) { // @phpstan-ignore-line
            return true; // check again after updating the database
        }
        glsr_log()->error("The ratings table could not be altered, the [terms] column was not added.");
        return false;
    }

    protected function repairDatabase(): void
    {
        require_once ABSPATH.'wp-admin/includes/plugin.php';
        if (!is_plugin_active_for_network(glsr()->basename)) {
            $this->install();
            return;
        }
        foreach (glsr(Install::class)->sites() as $siteId) {
            switch_to_blog($siteId);
            $this->install();
            restore_current_blog();
        }
    }
}
