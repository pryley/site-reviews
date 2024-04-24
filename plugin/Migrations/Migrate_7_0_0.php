<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;

class Migrate_7_0_0 implements MigrateContract
{
    public function run(): bool
    {
        delete_option(OptionManager::databaseKey(5));
        delete_transient(glsr()->prefix.'cloudflare_ips');
        $this->migrateDatabase();
        return true;
    }

    public function migrateDatabase(): void
    {
        if ($this->insertTableColumn('is_flagged', 'is_pinned')) {
            update_option(glsr()->prefix.'db_version', '1.3');
        }
    }

    protected function insertTableColumn(string $column, string $afterColumn): bool
    {
        if (glsr(Tables::class)->columnExists('ratings', $column)) {
            return true;
        }
        if (glsr(Tables::class)->isSqlite()) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD {$column} tinyint(1) NOT NULL DEFAULT '0'
            ");
        } else {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD {$column} tinyint(1) NOT NULL DEFAULT '0'
                AFTER {$afterColumn}
            ");
        }
        if (false === glsr(Database::class)->dbQuery($sql)) {
            glsr_log()->error("The ratings table could not be altered, the [{$column}] column was not added.");
            return false;
        }
        return true;
    }
}
