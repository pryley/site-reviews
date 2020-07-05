<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Review;

class Query
{
    use QuerySql;

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
    public function ratings(array $args = [])
    {
        $this->setArgs($args);
        $join = implode(' ', $this->sqlClauses([], 'join'));
        $and = implode(' ', $this->sqlClauses([], 'and'));
        $results = $this->db->get_results("
            SELECT r.rating, r.type, COUNT(r.rating) AS count
            FROM {$this->table('ratings')} AS r {$join}
            WHERE r.is_approved = 1 {$and}
            GROUP BY r.type, r.rating
        ", ARRAY_A);
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
        if (($review = glsr(Cache::class)->get($reviewId, 'reviews')) instanceof Review) {
            return $review;
        }
        $result = $this->db->get_row("
            {$this->sqlSelect()}
            {$this->sqlFrom()}
            {$this->sqlJoin()}
            {$this->sqlJoinPivots()}
            WHERE r.review_id = {$reviewId}
            GROUP BY r.ID
        ");
        $review = new Review($result);
        if (!empty($result)) {
            glsr(Cache::class)->store($reviewId, 'reviews', $review);
        }
        return $review;
    }

    /**
     * @return array
     */
    public function reviews(array $args = [], array $postIds = [])
    {
        $this->setArgs($args);
        if (!empty($postIds)) {
            $postIds = implode(',', Arr::uniqueInt($postIds));
        } else {
            $postIds = "SELECT ids.* FROM (
                SELECT r.review_id
                {$this->sqlFrom()}
                {$this->sqlJoinClauses()}
                {$this->sqlWhere()}
                {$this->sqlOrderBy()}
                {$this->sqlLimit()}
                {$this->sqlOffset()}
            ) as ids";
        }
        $results = $this->db->get_results("
            {$this->sqlSelect()}
            {$this->sqlFrom()}
            {$this->sqlJoin()}
            {$this->sqlJoinPivots()}
            WHERE r.review_id in ({$postIds})
            GROUP BY r.ID 
        ");
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
        return $this->db->get_col($this->db->prepare("
            SELECT ID
            FROM {$this->db->posts}
            WHERE post_parent = %d AND post_type = 'revision'
        ", $postId));
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
        return (int) $this->db->get_var("
            SELECT COUNT(*)
            {$this->sqlFrom()}
            {$this->sqlJoin()}
            {$this->sqlWhere()}
        ");
    }

    /**
     * @param int $postId
     * @return bool
     */
    public function hasRevisions($postId)
    {
        $revisions = (int) $this->db->get_var("
            SELECT COUNT(*) 
            FROM {$this->db->posts}
            WHERE post_type = 'revision' AND post_parent = {$postId}
        ");
        return $revisions > 0;
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
     * @return void
     */
    public function setArgs(array $args = [])
    {
        $this->args = (new NormalizeQueryArgs($args))->toArray();
    }
}
