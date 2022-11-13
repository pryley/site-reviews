<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;

class Migrate_6_3_0 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateDatabaseIndexes();
        return true;
    }

    /**
     * Fixes the PRIMARY indexes of the assigned tables.
     * The method used in the 6_0_0 migration did not work with MariaDB.
     */
    protected function migrateDatabaseIndexes(): void
    {
        $tables = [
            TableAssignedPosts::class => 'post_id',
            TableAssignedTerms::class => 'term_id',
            TableAssignedUsers::class => 'user_id',
        ];
        foreach ($tables as $table => $columnName) {
            $table = glsr($table);
            if (!glsr(Tables::class)->isInnodb($table->name)) {
                continue;
            }
            $constraints = glsr(Database::class)->dbGetCol("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = '{$table->dbname}' AND TABLE_NAME = '{$table->tablename}'
            ");
            // 1. Drop foreign constraints
            $table->dropForeignConstraints();
            if (!in_array('PRIMARY', $constraints)) {
                // 2. Drop unique index
                $uniqueIndex = sprintf('%s%s_rating_id_%s_unique', glsr()->prefix, $table->name, $columnName);
                if (in_array($uniqueIndex, $constraints)) {
                    glsr(Database::class)->dbQuery("
                        ALTER TABLE {$table->tablename} DROP INDEX {$uniqueIndex}
                    ");
                }
                // 3. Add primary key (replaces the unique index)
                glsr(Database::class)->dbQuery("
                    ALTER TABLE {$table->tablename} ADD PRIMARY KEY (rating_id,{$columnName})
                ");
            }
            // 4. Add foreign constraints
            $table->addForeignConstraints();
        }
    }
}
