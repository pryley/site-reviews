<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;

class Migrate_8_0_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->migrateDatabase();
        $this->migrateDatabaseVersion();
        return true;
    }

    public function migrateDatabase(): void
    {
        glsr(TableStats::class)->create();
        glsr(TableStats::class)->addForeignConstraints();
    }

    public function migrateDatabaseVersion(): void
    {
        if (glsr(TableStats::class)->exists()) {
            update_option(glsr()->prefix.'db_version', '1.5');
        }
    }
}
