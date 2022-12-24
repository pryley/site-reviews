<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Request;

class ImportProductReviews implements Contract
{
    public const PER_PAGE = 25;

    public $offset;
    public $request;

    public function __construct(Request $request)
    {
        $this->offset = max(0, ((int) $request->page - 1) * static::PER_PAGE); // @phpstan-ignore-line
        $this->request = $request;
    }

    public function handle()
    {
        define('WP_IMPORTING', true);
        wp_raise_memory_limit('admin');
        $processed = 0;
        $reviews = $this->reviews();
        foreach ($reviews as $values) {
            $values = array_map('trim', $values);
            $request = new Request($values);
            $command = new CreateReview($request);
            if (glsr(ReviewManager::class)->create($command)) {
                update_comment_meta((int) $values['comment_ID'], 'imported', 1);
                ++$processed;
            }
        }
        return [
            'processed' => $processed,
        ];
    }

    public function reviewIds(): array
    {
        $limit = static::PER_PAGE;
        $query = glsr(Query::class);
        $sql = $query->sql("
            SELECT DISTINCT c.comment_ID
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
            LIMIT {$limit}
            OFFSET {$this->offset}
        ");
        return glsr(Database::class)->dbGetCol($sql);
    }

    public function reviews(): array
    {
        $reviewIds = $this->reviewIds();
        $reviewIds = implode(',', Arr::uniqueInt(Cast::toArray($reviewIds)));
        $reviewIds = Str::fallback($reviewIds, '0'); // if there are no comment IDs, default to 0
        $query = glsr(Query::class);
        $sql = $query->sql("
            SELECT 
                c.comment_ID,
                c.comment_date as date,
                c.comment_date_gmt as date_gmt,
                cm.meta_value as rating,
                c.comment_content as content,
                c.comment_author as name,
                c.comment_author_email as email,
                c.comment_author_IP as ip_address,
                c.comment_approved as is_approved,
                c.comment_post_ID as assigned_posts,
                c.user_id as user_id
            FROM {$query->table('comments')} AS c
            INNER JOIN {$query->table('commentmeta')} AS cm ON c.comment_ID = cm.comment_id
            WHERE 1=1
            AND c.comment_ID IN ({$reviewIds})
            AND cm.meta_key = 'rating'
        ");
        return glsr(Database::class)->dbGetResults($sql, ARRAY_A);
    }
}
