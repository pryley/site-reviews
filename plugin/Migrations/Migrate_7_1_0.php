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
        $isDirty = false;
        $indexes = glsr(Database::class)->dbGetResults(
            glsr(Query::class)->sql("SHOW INDEXES FROM table|ratings")
        );
        $keyNames = wp_list_pluck($indexes, 'Key_name');
        if (!in_array('glsr_ratings_rating_type_is_approved_index', $keyNames)) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings ADD INDEX glsr_ratings_rating_type_is_approved_index (rating,type,is_approved)
            ");
            if (false === glsr(Database::class)->dbQuery($sql)) {
                $isDirty = true;
                glsr_log()->error("The ratings table could not be altered, the [rating_type_is_approved] index was not added.");
            }
        }
        if (!in_array('glsr_ratings_is_flagged_index', $keyNames)) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings ADD INDEX glsr_ratings_is_flagged_index (is_flagged)
            ");
            if (false === glsr(Database::class)->dbQuery($sql)) {
                $isDirty = true;
                glsr_log()->error("The ratings table could not be altered, the [is_flagged] index was not added.");
            }
        }
        if (!$isDirty) {
            update_option(glsr()->prefix.'db_version', '1.4');
        }
    }
}
