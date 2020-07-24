<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class Query
{
    use Sql;

    public $args;
    public $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @return array
     */
    public function export(array $args = [])
    {
        $this->setArgs($args);
        return $this->db->get_results($this->queryExport(), ARRAY_A);
    }

    /**
     * @param int $postId
     * @return bool
     */
    public function hasRevisions($postId)
    {
        return (int) $this->db->get_var($this->queryHasRevisions($postId)) > 0;
    }

    /**
     * @return array
     */
    public function import(array $args = [])
    {
        $this->setArgs($args);
        return $this->db->get_results($this->queryImport(), ARRAY_A);
    }

    /**
     * @return array
     */
    public function ratings(array $args = [])
    {
        $this->setArgs($args);
        $results = $this->db->get_results($this->queryRatings(), ARRAY_A);
        return $this->normalizeRatings($results);
    }

    /**
     * @todo make sure we delete the cached review when modifying it
     * @param int $postId
     * @return Review
     */
    public function review($postId)
    {
        $reviewId = Cast::toInt($postId);
        $review = glsr(Cache::class)->get($reviewId, 'reviews');
        if (!$review instanceof Review) {
            $result = $this->db->get_row($this->queryReviews($reviewId));
            $review = new Review($result);
            if ($review->isValid()) {
                glsr(Cache::class)->store($review->ID, 'reviews', $review);
            }
        }
        return $review;
    }

    /**
     * @return array
     */
    public function reviews(array $args = [], array $postIds = [])
    {
        $this->setArgs($args);
        if (empty($postIds)) {
            $reviewIds = $this->queryReviewIds();
        } else {
            $reviewIds = implode(',', Arr::uniqueInt(Cast::toArray($postIds)));
        }
        $results = $this->db->get_results($this->queryReviews($reviewIds));
        foreach ($results as &$result) {
            $result = new Review($result);
            glsr(Cache::class)->store($result->ID, 'reviews', $result);
        }
        return $results;
    }

    /**
     * @param int $postId
     * @return array
     */
    public function revisionIds($postId)
    {
        return $this->db->get_col($this->queryRevisionIds($postId));
    }

    /**
     * @return int
     */
    public function totalReviews(array $args = [], array $reviews = [])
    {
        $this->setArgs($args);
        if (empty($this->sqlLimit()) && !empty($reviews)) {
            return count($reviews);
        }
        return (int) $this->db->get_var($this->queryTotalReviews());
    }

    /**
     * @return array
     */
    protected function normalizeRatings(array $ratings = [])
    {
        $normalized = [];
        foreach ($ratings as $result) {
            $type = $result['type'];
            if (!array_key_exists($type, $normalized)) {
                $normalized[$type] = glsr(Rating::class)->emptyArray();
            }
            $normalized[$type] = Arr::set($normalized[$type], $result['rating'], $result['count']);
        }
        return $normalized;
    }

    /**
     * @return string
     */
    protected function queryExport()
    {
        return $this->sql("
            SELECT r.*,
                GROUP_CONCAT(DISTINCT apt.post_id) AS post_ids,
                GROUP_CONCAT(DISTINCT aut.user_id) AS user_ids
            FROM {$this->table('ratings')} AS r
            LEFT JOIN {$this->table('assigned_posts')} AS apt ON r.ID = apt.rating_id
            LEFT JOIN {$this->table('assigned_users')} AS aut ON r.ID = aut.rating_id
            GROUP BY r.ID
            ORDER BY r.ID
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ");
    }

    /**
     * @return string
     */
    protected function queryHasRevisions($reviewId)
    {
        return $this->sql($this->db->prepare("
            SELECT COUNT(*) 
            FROM {$this->db->posts}
            WHERE post_type = 'revision' AND post_parent = %d
        ", $reviewId));
    }

    /**
     * @return string
     */
    protected function queryImport()
    {
        return $this->sql($this->db->prepare("
            SELECT m.post_id, m.meta_value
            FROM {$this->db->postmeta} AS m
            INNER JOIN {$this->db->posts} AS p ON m.post_id = p.ID
            WHERE p.post_type = %s AND m.meta_key = %s
            ORDER BY m.meta_id
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ", glsr()->post_type, glsr()->export_key));
    }

    /**
     * @return string
     */
    protected function queryRatings()
    {
        return $this->sql("
            SELECT r.rating, r.type, COUNT(r.rating) AS count
            FROM {$this->table('ratings')} AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            GROUP BY r.type, r.rating
        ");
    }

    /**
     * @return string
     */
    protected function queryReviewIds()
    {
        $sql = $this->sql("
            SELECT r.review_id
            FROM {$this->table('ratings')} AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
            GROUP BY r.review_id
            {$this->sqlOrderBy()}
            {$this->sqlLimit()}
            {$this->sqlOffset()}
        ");
        return "SELECT ids.* FROM ({$sql}) AS ids";
    }

    /**
     * @param int|string $postIds
     * @return string
     */
    protected function queryReviews($reviewIds)
    {
        return $this->sql("
            SELECT
                r.*,
                p.post_author AS author_id,
                p.post_date AS date,
                p.post_content AS content,
                p.post_title AS title,
                p.post_status AS status,
                GROUP_CONCAT(DISTINCT apt.post_id) AS post_ids,
                GROUP_CONCAT(DISTINCT att.term_id) AS term_ids,
                GROUP_CONCAT(DISTINCT aut.user_id) AS user_ids
            FROM {$this->table('ratings')} AS r
            INNER JOIN {$this->db->posts} AS p ON r.review_id = p.ID
            LEFT JOIN {$this->table('assigned_posts')} AS apt ON r.ID = apt.rating_id
            LEFT JOIN {$this->table('assigned_terms')} AS att ON r.ID = att.rating_id
            LEFT JOIN {$this->table('assigned_users')} AS aut ON r.ID = aut.rating_id
            WHERE r.review_id IN ({$reviewIds})
            GROUP BY r.ID
        ");
    }

    /**
     * @return string
     */
    protected function queryRevisionIds($reviewId)
    {
        return $this->sql($this->db->prepare("
            SELECT ID
            FROM {$this->db->posts}
            WHERE post_type = 'revision' AND post_parent = %d
        ", $reviewId));
    }

    /**
     * @return string
     */
    protected function queryTotalReviews()
    {
        return $this->sql("
            SELECT COUNT(DISTINCT r.ID) AS count
            FROM {$this->table('ratings')} AS r
            {$this->sqlJoin()}
            {$this->sqlWhere()}
        ");
    }

    /**
     * @return void
     */
    protected function setArgs(array $args = [])
    {
        $this->args = (new NormalizeQueryArgs($args))->toArray();
    }
}
