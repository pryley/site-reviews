<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database\OptionManager;

class Migrate_6_0_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $version = 4; // remove settings from versions older than v5
        while ($version) {
            delete_option(OptionManager::databaseKey($version--));
        }
        $this->migrateDatabaseSchema();
        $this->migrateAddonBlocks();
        $this->migrateAddonImages();
        update_option(glsr()->prefix.'db_version', '1.2');
        return true;
    }

    protected function migrateAddonBlocks(): void
    {
        if (glsr()->addon('site-reviews-filters')) {
            global $wpdb;
            $wpdb->query("
                UPDATE {$wpdb->posts} p
                SET p.post_content = REPLACE(p.post_content, '<!-- wp:site-reviews/filter ', '<!-- wp:site-reviews/filters ')
                WHERE p.post_status = 'publish'
            ");
        }
    }

    protected function migrateAddonImages(): void
    {
        if (glsr()->addon('site-reviews-images')) {
            global $wpdb;
            $wpdb->query("
                UPDATE {$wpdb->posts} p
                SET p.post_status = 'inherit'
                WHERE p.post_type = 'attachment' AND p.post_name LIKE 'site-reviews-image%'
            ");
        }
    }

    protected function migrateDatabaseSchema(): void
    {
        global $wpdb;
        $indexes = [
            'assigned_posts' => 'post_id',
            'assigned_terms' => 'term_id',
            'assigned_users' => 'user_id',
        ];
        foreach ($indexes as $pivotTable => $columnName) {
            if (!glsr(SqlSchema::class)->isInnodb($pivotTable)) {
                continue;
            }
            $table = glsr(SqlSchema::class)->table($pivotTable);
            $uniqueIndex = "glsr_{$pivotTable}_rating_id_{$columnName}_unique";
            $constraints = $wpdb->get_col("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = '{$wpdb->dbname}' AND TABLE_NAME = '{$table}'
            ");
            // add primary key
            if (!in_array('PRIMARY', $constraints)) {
                $wpdb->query("ALTER TABLE {$table} ADD PRIMARY KEY (rating_id,{$columnName})");
            }
            // remove unique key
            if (in_array($uniqueIndex, $constraints)) {
                $wpdb->query("ALTER TABLE {$table} DROP INDEX {$uniqueIndex}");
            }
        }
    }
}
