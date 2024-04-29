<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands;

use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Request;

class ImportProductReviews extends AbstractCommand
{
    public const PER_PAGE = 25;

    public int $offset = 0;
    /** @var Request */
    public $request;
    public int $total = 0;

    public function __construct(Request $request)
    {
        $this->offset = max(0, ($request->cast('page', 'int') - 1) * static::PER_PAGE); // @phpstan-ignore-line
        $this->request = $request;
    }

    public function handle(): void
    {
        define('WP_IMPORTING', true);
        wp_raise_memory_limit('admin');
        $this->total = 0;
        $reviews = $this->reviews();
        foreach ($reviews as $values) {
            $values = array_map('trim', $values);
            $request = new Request($values);
            $command = new CreateReview($request);
            if (glsr(ReviewManager::class)->create($command)) {
                update_comment_meta((int) $values['comment_ID'], 'imported', 1);
                ++$this->total;
            }
        }
    }

    public function response(): array
    {
        return [
            'processed' => $this->total,
        ];
    }

    public function reviewIds(): array
    {
        $sql = "
            SELECT DISTINCT c.comment_ID
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
            LIMIT %d
            OFFSET %d
        ";
        return glsr(Database::class)->dbGetCol(
            glsr(Query::class)->sql($sql, static::PER_PAGE, $this->offset)
        );
    }

    public function reviews(): array
    {
        $reviewIds = $this->reviewIds();
        $reviewIds = implode(',', Arr::uniqueInt(Cast::toArray($reviewIds)));
        $reviewIds = Str::fallback($reviewIds, '0'); // if there are no comment IDs, default to 0
        $sql = glsr(Query::class)->sql("
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
            FROM table|comments AS c
            INNER JOIN table|commentmeta AS cm ON (cm.comment_id = c.comment_ID)
            WHERE 1=1
            AND c.comment_ID IN ({$reviewIds})
            AND cm.meta_key = 'rating'
        ");
        return glsr(Database::class)->dbGetResults($sql, ARRAY_A);
    }
}
