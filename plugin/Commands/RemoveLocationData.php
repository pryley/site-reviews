<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;
use GeminiLabs\SiteReviews\Modules\Notice;

class RemoveLocationData extends AbstractCommand
{
    public function handle(): void
    {
        if (glsr(Tables::class)->isSqlite()) { // the SQLite plugin can't run the joined DELETE
            $table = glsr(Tables::class)->table('postmeta');
            $sql = glsr(Query::class)->sql("
                DELETE FROM %i
                WHERE meta_key = '_geolocation'
                AND EXISTS (
                    SELECT 1
                    FROM table|posts AS p
                    WHERE p.ID = %i.post_id AND p.post_type = %s
                )
            ", $table, $table, glsr()->post_type);
        } else {
            $sql = glsr(Query::class)->sql("
                DELETE pm
                FROM table|postmeta AS pm
                INNER JOIN table|posts AS p ON (p.ID = pm.post_id)
                WHERE p.post_type = %s
                AND pm.meta_key = '_geolocation'
            ", glsr()->post_type);
        }
        glsr(Database::class)->dbQuery($sql);
        glsr(TableStats::class)->empty();
        glsr(Notice::class)->addSuccess(
            _x('Successfully removed the geolocation data from all reviews.', 'admin-text', 'site-reviews')
        );
        glsr()->action('cache/flush_all', 'removed_geolocation_data');
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
        ];
    }
}
