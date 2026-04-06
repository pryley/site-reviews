<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\Tables\TableStats;
use GeminiLabs\SiteReviews\Modules\Notice;

class RemoveLocationData extends AbstractCommand
{
    public function handle(): void
    {
        $sql = "
            DELETE pm
            FROM table|postmeta AS pm
            INNER JOIN table|posts AS p ON (p.ID = pm.post_id)
            WHERE p.post_type = %s
            AND pm.meta_key = '_geolocation'
        ";
        glsr(Database::class)->dbQuery(
            glsr(Query::class)->sql($sql, glsr()->post_type)
        );
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
