<?php

namespace GeminiLabs\SiteReviews\Migrations\Migrate_8_0_0;

use GeminiLabs\SiteReviews\Contracts\MigrateContract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;

class MigrateReviewForms implements MigrateContract
{
    /**
     * Run migration.
     */
    public function run(): bool
    {
        $sql = "
            UPDATE table|postmeta pm
            LEFT JOIN table|postmeta pm2 ON (
                pm2.post_id = pm.post_id AND pm2.meta_key = '_form'
            )
            SET pm.meta_key = '_form'
            WHERE 1=1
            AND pm2.post_id IS NULL
            AND pm.meta_key = '_custom_form'
            AND pm.meta_value > '0'
            AND pm.post_id IN (
                SELECT ID
                FROM table|posts
                WHERE post_type = %s
            )
        ";
        $query = glsr(Query::class)->sql($sql, glsr()->post_type);
        glsr(Database::class)->dbQuery($query);
        return true;
    }
}
