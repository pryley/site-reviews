<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class Migrate_7_1_0 implements MigrateContract
{
    public function run(): bool
    {
        $this->migrateDatabase();
        return true;
    }

    public function migrateDatabase(): void
    {
        $indexedColumns = [
            'is_approved',
            'is_flagged',
        ];
        $indexes = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql("SHOW INDEXES FROM table|ratings")
        );
        $isDirty = false;
        foreach ($indexedColumns as $column) {
            $indexExists = false;
            foreach ($indexes as $index) {
                if ("glsr_ratings_{$column}_index" === $index->Key_name && $column === $index->Column_name) {
                    $indexExists = true;
                    break;
                }
            }
            if ($indexExists) {
                continue;
            }
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings ADD INDEX glsr_ratings_{$column}_index ({$column})
            ");
            if (false === glsr(Database::class)->dbQuery($sql)) {
                $isDirty = true;
                glsr_log()->error("The ratings table could not be altered, the [is_approved] index was not added.");
            }
        }
        if (!$isDirty) {
            update_option(glsr()->prefix.'db_version', '1.4');
        }
    }
}
