<?php

namespace GeminiLabs\SiteReviews\Migrations;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedPosts;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedTerms;
use GeminiLabs\SiteReviews\Database\Tables\TableAssignedUsers;

class Migrate_6_2_1 implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $this->migrateDatabaseIndexes();
        $this->removeDuplicateCustomFields();
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
                WHERE CONSTRAINT_SCHEMA = '{$table->database}' AND TABLE_NAME = '{$table->tablename}'
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

    /**
     * Removes duplicate custom field values (keeps the last duplicate record).
     */
    protected function removeDuplicateCustomFields(): void
    {
        $sql = "
            DELETE pm
            FROM table|postmeta AS pm
            INNER JOIN table|posts AS p ON (p.ID = pm.post_id)
            WHERE p.post_type = %s
            AND pm.meta_key LIKE '_custom_%%'
            AND pm.meta_id NOT IN (
                SELECT *
                FROM (
                    SELECT MAX(meta_id)
                    FROM table|postmeta
                    WHERE meta_key LIKE '_custom_%%'
                    GROUP BY post_id, meta_key
                ) AS x
            )
        ";
        glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql($sql, glsr()->post_type)
        );
    }
}
