<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Request;

class CountProductReviews extends AbstractCommand
{
    public Request $request;

    protected int $total;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(): void
    {
        $sql = glsr(Query::class)->sql("
            SELECT COUNT(DISTINCT c.comment_ID)
            FROM table|comments AS c
            INNER JOIN table|commentmeta AS cm ON (cm.comment_id = c.comment_ID)
            WHERE 1=1
            AND c.comment_type = 'review'
            AND c.comment_approved IN ('0','1')
            AND c.comment_parent = 0
            AND cm.meta_key = 'rating'
            AND NOT EXISTS (
                SELECT NULL
                FROM table|commentmeta AS cm2
                WHERE 1=1
                AND cm2.comment_id = c.comment_ID
                AND cm2.meta_key = 'imported'
            )
        ");
        $this->total = (int) glsr(Database::class)->dbGetVar($sql);
    }

    public function response(): array
    {
        return [
            'total' => $this->total,
        ];
    }
}
