<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
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
        $this->migrateAddonImages();
        $this->migrateDatabase();
        $this->migrateDatabaseSchema();
        $this->migrateRoles();
        $this->migrateSettings();
        return true;
    }

    public function migrateAddonBlocks(): void
    {
        if (glsr()->addon('site-reviews-filters')) {
            global $wpdb;
            glsr(Database::class)->dbQuery("
                UPDATE {$wpdb->posts} p
                SET p.post_content = REPLACE(p.post_content, '<!-- wp:site-reviews/filter ', '<!-- wp:site-reviews/filters ')
                WHERE p.post_status = 'publish'
            ");
        }
    }

    public function migrateAddonImages(): void
    {
        if (glsr()->addon('site-reviews-images')) {
            global $wpdb;
            glsr(Database::class)->dbQuery("
                UPDATE {$wpdb->posts} p
                SET p.post_status = 'inherit'
                WHERE p.post_type = 'attachment' AND p.post_name LIKE 'site-reviews-image%'
            ");
        }
    }

    public function migrateDatabase(): void
    {
        $result = true;
        if (!$this->insertTableColumnIsVerified()) {
            $result = false;
        }
        if (!$this->insertTableColumnScore()) {
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

    public function migrateDatabaseSchema(): void
    {
        global $wpdb;
        $indexes = [
            'assigned_posts' => 'post_id',
            'assigned_terms' => 'term_id',
            'assigned_users' => 'user_id',
        ];
        foreach ($indexes as $assignedTable => $columnName) {
            if (!glsr(Tables::class)->isInnodb($assignedTable)) {
                continue;
            }
            $table = glsr(Tables::class)->table($assignedTable);
            $uniqueIndex = "glsr_{$assignedTable}_rating_id_{$columnName}_unique";
            $constraints = glsr(Database::class)->dbGetCol("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = '{$wpdb->dbname}' AND TABLE_NAME = '{$table}'
            ");
            // add primary key first!
            if (!in_array('PRIMARY', $constraints)) {
                glsr(Database::class)->dbQuery("
                    ALTER TABLE {$table}
                    ADD PRIMARY KEY (rating_id,{$columnName})
                ");
            }
            // remove unique key
            if (in_array($uniqueIndex, $constraints)) {
                glsr(Database::class)->dbQuery("
                    ALTER TABLE {$table}
                    DROP INDEX {$uniqueIndex}
                ");
            }
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
        if ($settings = get_option(OptionManager::databaseKey(5))) {
            $forms = Arr::get($settings, 'settings.submissions');
            $settings = Arr::set($settings, 'settings.forms', $forms);
            unset($settings['settings']['submissions']);
            update_option(OptionManager::databaseKey(6), $settings);
        }
        $style = glsr(OptionManager::class)->get('settings.general.style');
        if (in_array($style, ['bootstrap_4', 'bootstrap_4_custom'])) {
            glsr(OptionManager::class)->set('settings.general.style', 'bootstrap');
        }
    }

    protected function insertTableColumnIsVerified(): bool
    {
        $table = glsr(Tables::class)->table('ratings');
        if (!glsr(Tables::class)->columnExists('ratings', 'is_verified')) {
            glsr(Database::class)->dbQuery("
                ALTER TABLE {$table}
                ADD is_verified tinyint(1) NOT NULL DEFAULT '0'
                AFTER is_pinned
            ");
        }
        if (!glsr(Tables::class)->columnExists('ratings', 'is_verified')) {
            glsr_log()->error(sprintf('Database table [%s] could not be altered, column [is_verified] was not added.', $table));
            return false;
        }
        return true;
    }

    protected function insertTableColumnScore(): bool
    {
        $table = glsr(Tables::class)->table('ratings');
        if (!glsr(Tables::class)->columnExists('ratings', 'score')) {
            glsr(Database::class)->dbQuery("
                ALTER TABLE {$table}
                ADD score tinyint(1) NOT NULL DEFAULT '0'
                AFTER terms
            ");
        }
        if (!glsr(Tables::class)->columnExists('ratings', 'score')) {
            glsr_log()->error(sprintf('Database table [%s] could not be altered, column [score] was not added.', $table));
            return false;
        }
        return true;
    }
}
