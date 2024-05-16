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
        foreach ($reviews as $commentId => $values) {
            $values = (array) $values;
            $values = array_map('trim', $values);
            $request = new Request($values);
            $command = new CreateReview($request);
            if (glsr(ReviewManager::class)->create($command)) {
                update_comment_meta((int) $commentId, 'imported', 1);
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

    /**
     * @return int[]
     */
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
        $sql = glsr(Query::class)->sql($sql, static::PER_PAGE, $this->offset);
        $results = glsr(Database::class)->dbGetCol($sql);
        return Arr::uniqueInt($results);
    }

    /**
     * @return object[]
     */
    public function reviews(): array
    {
        $reviewIds = $this->reviewIds();
        $reviewIds = implode(',', $reviewIds);
        $reviewIds = Str::fallback($reviewIds, '0'); // if there are no comment IDs, default to 0
        $sql = glsr(Query::class)->sql("
            SELECT 
                c.comment_ID,
                c.comment_date AS date,
                c.comment_date_gmt AS date_gmt,
                cm.meta_value AS rating,
                c.comment_content AS content,
                c.comment_author AS name,
                c.comment_author_email AS email,
                c.comment_author_IP AS ip_address,
                c.comment_approved AS is_approved,
                c.comment_post_ID AS assigned_posts,
                c.user_id AS user_id
            FROM table|comments AS c
            INNER JOIN table|commentmeta AS cm ON (cm.comment_id = c.comment_ID)
            WHERE 1=1
            AND c.comment_ID IN ({$reviewIds})
            AND cm.meta_key = 'rating'
        ");
        $reviews = glsr(Database::class)->dbGetResults($sql, OBJECT_K);
        if (empty($reviews)) {
            return [];
        }
        $verifiedCommentIds = $this->verifiedCommentIds($reviewIds);
        foreach ($reviews as $commentId => $values) {
            if (in_array((int) $commentId, $verifiedCommentIds)) {
                $values->verified = 1;
            }
        }
        return $reviews;
    }

    /**
     * @return int[]
     */
    public function verifiedCommentIds(string $reviewIds): array
    {
        $sql = glsr(Query::class)->sql("
            SELECT c.comment_ID
            FROM table|comments AS c
            INNER JOIN table|commentmeta AS cm ON (cm.comment_id = c.comment_ID)
            WHERE 1=1
            AND c.comment_ID IN ({$reviewIds})
            AND cm.meta_key = 'verified'
            AND cm.meta_value = 1
        ");
        $results = glsr(Database::class)->dbGetCol($sql);
        return Arr::uniqueInt($results);
    }
}
