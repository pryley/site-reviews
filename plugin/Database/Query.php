<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\ReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

/**
 * @property array $args
 * @property \wpdb $db
 */
class Query
{
    use Sql;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function export(array $args = []): array
    {
        $this->setArgs($args);
        return glsr(Database::class)->dbGetResults($this->queryExport(), ARRAY_A);
    }

    public function hasRevisions(int $postId): bool
    {
        return (int) glsr(Database::class)->dbGetVar($this->queryHasRevisions($postId)) > 0;
    }

    public function import(array $args = []): array
    {
        $this->setArgs($args);
        return glsr(Database::class)->dbGetResults($this->queryImport(), ARRAY_A);
    }

    public function ratings(array $args = []): array
    {
        $this->setArgs($args, $unset = ['orderby']);
        $results = glsr(Database::class)->dbGetResults($this->queryRatings(), ARRAY_A);
        return $this->normalizeRatings($results);
    }

    public function ratingsFor(string $metaType, array $args = []): array
    {
        $method = Helper::buildMethodName('queryRatingsFor', $metaType);
        if (!method_exists($this, $method)) {
            return [];
        }
        $this->setArgs($args, $unset = ['orderby']);
        $results = glsr(Database::class)->dbGetResults($this->$method(), ARRAY_A);
        return $this->normalizeRatingsByAssignedId($results);
    }

    public function review(int $postId, bool $bypassCache = false): Review
    {
        $reviewId = Cast::toInt($postId);
        $review = Helper::ifTrue($bypassCache, null,
            fn () => glsr(Cache::class)->get($reviewId, 'reviews')
        );
        if (!$review instanceof Review) {
            $result = glsr(Database::class)->dbGetRow($this->queryReviews($reviewId), ARRAY_A);
            $review = new Review($result);
            glsr()->action('get/review', $review, $reviewId);
            if ($review->isValid()) {
                glsr(Cache::class)->store($review->ID, 'reviews', $review);
            }
        }
        return $review;
    }

    public function reviewIds(array $args = []): array
    {
        $this->setArgs($args);
        $postIds = glsr(Database::class)->dbGetCol($this->queryReviewIds());
        return array_map('intval', $postIds);
    }

    public function reviews(array $args = [], array $postIds = []): array
    {
        if (!empty($postIds)) {
            $this->setArgs($args);
            $reviewIds = Arr::uniqueInt(Cast::toArray($postIds));
        } else {
            // We previously used a subquery here, but MariaDB doesn't support LIMIT in subqueries
            // https://mariadb.com/kb/en/subquery-limitations/
            $reviewIds = $this->reviewIds($args);
        }
        $reviewIds = implode(',', $reviewIds);
        $reviewIds = Str::fallback($reviewIds, '0'); // if there are no review IDs, default to 0
        $reviews = glsr(Database::class)->dbGetResults($this->queryReviews($reviewIds), ARRAY_A);
        foreach ($reviews as &$review) {
            $review = new Review($review);
            glsr()->action('get/review', $review, $review->ID);
            glsr(Cache::class)->store($review->ID, 'reviews', $review);
        }
        return $reviews;
    }

    public function revisionIds(int $postId): array
    {
        return glsr(Database::class)->dbGetCol($this->queryRevisionIds($postId));
    }

    public function setArgs(array $args = [], array $unset = []): void
    {
        $args = glsr(ReviewsDefaults::class)->restrict($args);
        foreach ($unset as $key) {
            $args[$key] = '';
        }
        $this->args = $args;
    }

    public function totalReviews(array $args = [], array $reviews = []): int
    {
        $this->setArgs($args, $unset = ['orderby']);
        if (empty($this->sqlLimit()) && !empty($reviews)) {
            return count($reviews);
        }
        return (int) glsr(Database::class)->dbGetVar($this->queryTotalReviews());
    }

    protected function normalizeRatings(array $ratings = []): array
    {
        $normalized = [];
        foreach ($ratings as $result) {
            $count = $result['count'] ?? 0;
            $rating = $result['rating'] ?? 0;
            $type = $result['type'] ?? 'local';
            if (!array_key_exists($type, $normalized)) {
                $normalized[$type] = glsr(Rating::class)->emptyArray();
            }
            if (array_key_exists($rating, $normalized[$type])) {
                $normalized[$type][$rating] = $count;
            }
        }
        return $normalized;
    }

    protected function normalizeRatingsByAssignedId(array $ratings = []): array
    {
        $normalized = [];
        foreach ($ratings as $result) {
            $id = $result['ID'];
            unset($result['ID']);
            if (!array_key_exists($id, $normalized)) {
                $normalized[$id] = [];
            }
            $normalized[$id][] = $result;
        }
        return array_map([$this, 'normalizeRatings'], $normalized);
    }

    protected function queryExport(): string
    {
        return $this->sql("
            SELECT r.*,
                GROUP_CONCAT(DISTINCT apt.post_id) AS post_ids,
                GROUP_CONCAT(DISTINCT aut.user_id) AS user_ids
            FROM table|ratings AS r
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            GROUP BY r.ID
            ORDER BY r.ID
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ");
    }

    protected function queryHasRevisions(int $reviewId): string
    {
        $sql = "
            SELECT COUNT(*)
            FROM table|posts
            WHERE post_type = 'revision' AND post_parent = %d
        ";
        return $this->sql($sql, $reviewId);
    }

    protected function queryImport(): string
    {
        $sql = "
            SELECT m.post_id, m.meta_value
            FROM table|postmeta AS m
            INNER JOIN table|posts AS p ON (p.ID = m.post_id)
            WHERE p.post_type = %s AND m.meta_key = %s
            ORDER BY m.meta_id
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ";
        return $this->sql($sql, glsr()->post_type, glsr()->export_key);
    }

    protected function queryRatings(): string
    {
        return $this->sql("
            SELECT {$this->ratingColumn()} AS rating, r.type, COUNT(DISTINCT r.ID) AS count
            FROM table|ratings AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            GROUP BY r.type, {$this->ratingColumn()}
        ");
    }

    public function queryRatingsForPostmeta(): string
    {
        return $this->sql("
            SELECT apt.post_id AS ID, {$this->ratingColumn()} AS rating, r.type, COUNT(DISTINCT r.ID) AS count
            FROM table|ratings AS r
            INNER JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            WHERE 1=1
            {$this->clauseAndStatus()}
            {$this->clauseAndType()}
            GROUP BY r.type, {$this->ratingColumn()}, apt.post_id
        ");
    }

    protected function queryRatingsForTermmeta(): string
    {
        return $this->sql("
            SELECT att.term_id AS ID, {$this->ratingColumn()} AS rating, r.type, COUNT(DISTINCT r.ID) AS count
            FROM table|ratings AS r
            INNER JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            WHERE 1=1
            {$this->clauseAndStatus()}
            {$this->clauseAndType()}
            GROUP BY r.type, {$this->ratingColumn()}, att.term_id
        ");
    }

    protected function queryRatingsForUsermeta(): string
    {
        return $this->sql("
            SELECT aut.user_id AS ID, {$this->ratingColumn()} AS rating, r.type, COUNT(DISTINCT r.ID) AS count
            FROM table|ratings AS r
            INNER JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            WHERE 1=1
            {$this->clauseAndStatus()}
            {$this->clauseAndType()}
            GROUP BY r.type, {$this->ratingColumn()}, aut.user_id
        ");
    }

    protected function queryReviewIds(): string
    {
        return $this->sql("
            SELECT r.review_id
            FROM table|ratings AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            GROUP BY r.review_id
            {$this->sqlOrderBy()}
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ");
    }

    /**
     * @param int|string $reviewIds
     */
    protected function queryReviews($reviewIds): string
    {
        $orderBy = !empty($this->args['order']) ? $this->sqlOrderBy() : '';
        $postType = glsr()->post_type;
        return $this->sql("
            SELECT
                r.*,
                p.post_author AS author_id,
                p.post_date AS date,
                p.post_date_gmt AS date_gmt,
                p.post_content AS content,
                p.post_title AS title,
                p.post_status AS status,
                GROUP_CONCAT(DISTINCT apt.post_id) AS post_ids,
                GROUP_CONCAT(DISTINCT att.term_id) AS term_ids,
                GROUP_CONCAT(DISTINCT aut.user_id) AS user_ids
            FROM table|ratings AS r
            INNER JOIN table|posts AS p ON (p.ID = r.review_id)
            LEFT JOIN table|assigned_posts AS apt ON (apt.rating_id = r.ID)
            LEFT JOIN table|assigned_terms AS att ON (att.rating_id = r.ID)
            LEFT JOIN table|assigned_users AS aut ON (aut.rating_id = r.ID)
            WHERE r.review_id IN ({$reviewIds}) AND p.post_type = '{$postType}'
            GROUP BY r.ID
            {$orderBy}
        ");
    }

    protected function queryRevisionIds(int $reviewId): string
    {
        $sql = "
            SELECT ID
            FROM table|posts
            WHERE post_type = 'revision' AND post_parent = %d
        ";
        return $this->sql($sql, $reviewId);
    }

    protected function queryTotalReviews(): string
    {
        return $this->sql("
            SELECT COUNT(DISTINCT r.ID) AS count
            FROM table|ratings AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
        ");
    }
}
