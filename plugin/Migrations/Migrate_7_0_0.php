<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables;

class Migrate_7_0_0 implements MigrateContract
{
    public function run(): bool
    {
        delete_transient(glsr()->prefix.'cloudflare_ips');
        $this->migrateDatabase();
        return true;
    }

    public function migrateDatabase(): void
    {
        $result = true;
        if (!$this->insertTableColumnFlagged()) {
            $result = false;
        }
        if ($result) {
            update_option(glsr()->prefix.'db_version', '1.3');
        }
    }

    protected function insertTableColumnFlagged(): bool
    {
        $table = glsr(Tables::class)->table('ratings');
        if (!glsr(Tables::class)->columnExists('ratings', 'is_flagged')) {
            glsr(Database::class)->dbQuery("
                ALTER TABLE {$table}
                ADD is_flagged tinyint(1) NOT NULL DEFAULT '0'
                AFTER is_pinned
            ");
        }
        if (!glsr(Tables::class)->columnExists('ratings', 'is_flagged')) {
            glsr_log()->error(sprintf('Database table [%s] could not be altered, column [is_flagged] was not added.', $table));
            return false;
        }
        return true;
    }
}
