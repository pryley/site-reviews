<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Modules\Migrate;

class Migrate_7_0_0 implements MigrateContract
{
    public function run(): bool
    {
        delete_option(OptionManager::databaseKey(5));
        delete_transient(glsr()->prefix.'cloudflare_ips');
        $this->migrateDatabase();
        $this->migrateSettings();
        return true;
    }

    public function migrateDatabase(): void
    {
        if ($this->insertTableColumn('is_flagged', 'is_pinned')) {
            update_option(glsr()->prefix.'db_version', '1.3');
        }
    }

    public function migrateSettings(): void
    {
        $settings = get_option(OptionManager::databaseKey());
        // remove last_migration_run from settings
        if (isset($settings['last_migration_run'])) {
            unset($settings['last_migration_run']);
        }
        // fix notification message
        if (isset($settings['settings']['general']['notification_message'])) {
            $search = [
                '{review_author}  - {review_ip}',
                '{review_link}',
            ];
            $replace = [
                '{review_author} ({review_email}) - {review_ip}',
                '<a href="{edit_url}">Edit Review</a>',
            ];
            $notification = str_replace($search, $replace, $settings['settings']['general']['notification_message']);
            $settings['settings']['general']['notification_message'] = $notification;
        }
        update_option(OptionManager::databaseKey(), $settings, true);
    }

    protected function insertTableColumn(string $column, string $afterColumn): bool
    {
        if (glsr(Tables::class)->columnExists('ratings', $column)) {
            return true;
        }
        if (glsr(Tables::class)->isSqlite()) {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD COLUMN {$column} tinyint(1) NOT NULL DEFAULT '0'
            ");
        } else {
            $sql = glsr(Query::class)->sql("
                ALTER TABLE table|ratings
                ADD COLUMN {$column} tinyint(1) NOT NULL DEFAULT '0'
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
