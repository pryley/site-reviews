<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableFields;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Role;

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
        $this->migrateAddonBlocks();
        $this->migrateAddonReviewImages();
        $this->migrateDatabase();
        $this->migrateRoles();
        $this->migrateSettings();
        return true;
    }

    public function migrateAddonBlocks(): void
    {
        if (glsr()->addon('site-reviews-filters')) {
            $sql = glsr(Query::class)->sql("
                UPDATE table|posts p
                SET p.post_content = REPLACE(p.post_content, '<!-- wp:site-reviews/filter ', '<!-- wp:site-reviews/filters ')
                WHERE p.post_status = 'publish'
            ");
            glsr(Database::class)->dbQuery($sql);
        }
    }

    public function migrateAddonReviewImages(): void
    {
        if (glsr()->addon('site-reviews-images')) {
            $sql = glsr(Query::class)->sql("
                UPDATE table|posts p
                SET p.post_status = 'inherit'
                WHERE p.post_type = 'attachment' AND p.post_name LIKE 'site-reviews-image%'
            ");
            glsr(Database::class)->dbQuery($sql);
        }
    }

    public function migrateDatabase(): void
    {
        $result = true;
        if (!$this->insertTableColumn('is_verified', 'is_pinned')) {
            $result = false;
        }
        if (!$this->insertTableColumn('score', 'terms')) {
            $result = false;
        }
        // @todo migrate custom fields to the fields table
        // if (glsr(TableFields::class)->create()) {
        //     glsr(TableFields::class)->addForeignConstraints();
        // } else {
        //     $result = false;
        // }
        if ($result) {
            update_option(glsr()->prefix.'db_version', '1.2');
        }
    }

    public function migrateRoles(): void
    {
        glsr(Role::class)->resetAll();
        $wpRoles = wp_roles();
        foreach ($wpRoles->roles as $role => $details) {
            $wpRole = $wpRoles->get_role($role);
            if (array_key_exists('edit_posts', $details['capabilities'])) {
                $wpRole->add_cap(glsr(Role::class)->capability('assign_terms'));
            }
            if (array_key_exists('manage_categories', $details['capabilities'])) {
                $wpRole->add_cap(glsr(Role::class)->capability('delete_terms'));
                $wpRole->add_cap(glsr(Role::class)->capability('edit_terms'));
                $wpRole->add_cap(glsr(Role::class)->capability('manage_terms'));
            }
        }
    }

    public function migrateSettings(): void
    {
        $oldSettings = Arr::consolidate(get_option(OptionManager::databaseKey(5)));
        $newSettings = Arr::consolidate(get_option(OptionManager::databaseKey(6)));
        if (empty($oldSettings)) {
            return;
        }
        if ($forms = Arr::get($newSettings, 'settings.submissions')) {
            $newSettings = Arr::set($newSettings, 'settings.forms', $forms);
        }
        $style = Arr::get($newSettings, 'settings.general.style');
        if (in_array($style, ['bootstrap_4', 'bootstrap_4_custom'])) {
            $newSettings = Arr::set($newSettings, 'settings.general.style', 'bootstrap');
        }
        unset($newSettings['settings']['submissions']);
        update_option(OptionManager::databaseKey(6), $newSettings, true);
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
