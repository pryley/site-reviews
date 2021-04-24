<?php

namespace GeminiLabs\SiteReviews\Modules\Migrations;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\SqlSchema;

class Migrate_5_9_0
{
    /**
     * @return bool
     */
    public function migrateDatabase()
    {
        $table = glsr(SqlSchema::class)->table('ratings');
        if (glsr(SqlSchema::class)->columnExists($table, 'terms')) {
            return true;
        }
        glsr(Database::class)->dbQuery(glsr(Query::class)->sql("
            ALTER TABLE {$table}
            ADD terms tinyint(1) NOT NULL DEFAULT '1'
            AFTER url
        "));
        if (glsr(SqlSchema::class)->columnExists($table, 'terms')) {
            update_option(glsr()->prefix.'db_version', '1.1');
            return true;
        }
        glsr_log()->error(sprintf('Database table [%s] could not be altered, column [terms] not added.', $table));
        return false;
    }

    /**
     * @return bool
     */
    public function run()
    {
        return $this->migrateDatabase();
    }
}
