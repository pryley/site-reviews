<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Request;

class CountProductReviews implements Contract
{
    public const PER_PAGE = 25;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $query = glsr(Query::class);
        $sql = $query->sql("
            SELECT COUNT(DISTINCT c.comment_ID)
            FROM {$query->table('comments')} AS c
            INNER JOIN {$query->table('commentmeta')} AS cm ON c.comment_ID = cm.comment_id
            WHERE 1=1
            AND c.comment_type = 'review'
            AND c.comment_approved IN ('0','1')
            AND c.comment_parent = 0
            AND cm.meta_key = 'rating'
            AND NOT EXISTS (
                SELECT NULL
                FROM {$query->table('commentmeta')} AS cm2
                WHERE 1=1
                AND cm2.comment_id = c.comment_ID
                AND cm2.meta_key = 'imported'
            )
        ");
        $total = (int) glsr(Database::class)->dbGetVar($sql);
        return [
            'notice' => esc_html_x('Imported %d Product Reviews', 'admin-text', 'site-reviews'),
            'pages' => (int) ceil($total / static::PER_PAGE),
            'total' => $total,
        ];
    }
}
