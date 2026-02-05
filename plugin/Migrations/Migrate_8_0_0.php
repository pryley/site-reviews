<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;
use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0\MigrateElementor;
use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0\MigrateFusionBuilder;
use GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0\MigrateReviewForms;

class Migrate_8_0_0 implements MigrateContract
{
    protected bool $updateElementor;

    public function run(): bool
    {
        glsr(MigrateElementor::class)->run();
        glsr(MigrateFusionBuilder::class)->run();
        glsr(MigrateReviewForms::class)->run();
        return $this->migrateDatabase();
    }

    public function migrateDatabase(): bool
    {
        $isDirty = false;
        $indexes = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql("SHOW INDEXES FROM table|ratings")
        );
        $keyNames = wp_list_pluck($indexes, 'Key_name');
        if (!in_array('glsr_ratings_ip_address_index', $keyNames)) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings ADD INDEX glsr_ratings_ip_address_index (ip_address)
            ");
            if (false === glsr(Database::class)->dbQuery($sql)) {
                glsr_log()->error("The ratings table could not be altered, the [ip_address_index] index was not added.");
                $isDirty = true;
            }
        }
        glsr(TableStats::class)->create();
        glsr(TableStats::class)->addForeignConstraints();
        if (!glsr(TableStats::class)->exists() || $isDirty) {
            return false;
        }
        update_option(glsr()->prefix.'db_version', '1.5');
        glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql("DELETE FROM table|usermeta WHERE meta_key = '_glsr_notices'")
        );
        return true;
    }
}
