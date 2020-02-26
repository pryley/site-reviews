<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class SqlQueries
{
    protected $db;
    protected $postType;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->postType = Application::POST_TYPE;
    }

    /**
     * @return bool
     */
    public function deletePostCountMetaKeys()
    {
        $this->db->query("
            DELETE
            FROM {$this->db->postmeta}
            WHERE meta_key LIKE '_glsr_%'
        ");
    }

    /**
     * @return bool
     */
    public function deleteTermCountMetaKeys()
    {
        $this->db->query("
            DELETE
            FROM {$this->db->termmeta}
            WHERE meta_key LIKE '_glsr_%'
        ");
    }

    /**
     * @param string $metaReviewId
     * @return int
     */
    public function getPostIdFromReviewId($metaReviewId)
    {
        $postId = $this->db->get_var("
            SELECT p.ID
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
            WHERE p.post_type = '{$this->postType}'
            AND m.meta_key = '_review_id'
            AND m.meta_value = '{$metaReviewId}'
        ");
        return intval($postId);
    }

    /**
     * @param int $lastPostId
     * @param int $limit
     * @return array
     */
    public function getReviewCounts(array $args, $lastPostId = 0, $limit = 500)
    {
        return (array) $this->db->get_results("
            SELECT DISTINCT p.ID, m1.meta_value AS rating, m2.meta_value AS type
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
            INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
            {$this->getInnerJoinForCounts($args)}
            WHERE p.ID > {$lastPostId}
            AND p.post_status = 'publish'
            AND p.post_type = '{$this->postType}'
            AND m1.meta_key = '_rating'
            AND m2.meta_key = '_review_type'
            {$this->getAndForCounts($args)}
            ORDER By p.ID ASC
            LIMIT {$limit}
        ");
    }

    /**
     * @todo remove this?
     * @param string $metaKey
     * @return array
     */
    public function getReviewCountsFor($metaKey)
    {
        $metaKey = Str::prefix('_', $metaKey);
        return (array) $this->db->get_results("
            SELECT DISTINCT m.meta_value AS name, COUNT(*) num_posts
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
            WHERE p.post_type = '{$this->postType}'
            AND m.meta_key = '{$metaKey}'
            GROUP BY name
        ");
    }

    /**
     * @todo remove this?
     * @param string $reviewType
     * @return array
     */
    public function getReviewIdsByType($reviewType)
    {
        $results = $this->db->get_col("
            SELECT DISTINCT m1.meta_value AS review_id
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS m1 ON p.ID = m1.post_id
            INNER JOIN {$this->db->postmeta} AS m2 ON p.ID = m2.post_id
            WHERE p.post_type = '{$this->postType}'
            AND m1.meta_key = '_review_id'
            AND m2.meta_key = '_review_type'
            AND m2.meta_value = '{$reviewType}'
        ");
        return array_keys(array_flip($results));
    }

    /**
     * @param int $greaterThanId
     * @param int $limit
     * @return array
     */
    public function getReviewRatingsFromIds(array $postIds, $greaterThanId = 0, $limit = 100)
    {
        sort($postIds);
        $postIds = array_slice($postIds, intval(array_search($greaterThanId, $postIds)), $limit);
        $postIds = implode(',', $postIds);
        return (array) $this->db->get_results("
            SELECT p.ID, m.meta_value AS rating
            FROM {$this->db->posts} AS p
            INNER JOIN {$this->db->postmeta} AS m ON p.ID = m.post_id
            WHERE p.ID > {$greaterThanId}
            AND p.ID IN ('{$postIds}')
            AND p.post_status = 'publish'
            AND p.post_type = '{$this->postType}'
            AND m.meta_key = '_rating'
            GROUP BY p.ID
            ORDER By p.ID ASC
            LIMIT {$limit}
        ");
    }

    /**
     * @param string $key
     * @param string $status
     * @return array
     */
    public function getReviewsMeta($key, $status = 'publish')
    {
        $postStatusQuery = 'all' != $status && !empty($status)
            ? "AND p.post_status = '{$status}'"
            : '';
        $key = Str::prefix('_', $key);
        $values = $this->db->get_col("
            SELECT DISTINCT m.meta_value
            FROM {$this->db->postmeta} m
            LEFT JOIN {$this->db->posts} p ON p.ID = m.post_id
            WHERE p.post_type = '{$this->postType}'
            AND m.meta_key = '{$key}'
            AND m.meta_value > '' -- No empty values or ID's less than 1
            $postStatusQuery
            GROUP BY p.ID -- remove duplicate meta_value entries
            ORDER BY m.meta_id ASC -- sort by oldest meta_value
        ");
        sort($values);
        return $values;
    }

    /**
     * @param string $and
     * @return string
     */
    protected function getAndForCounts(array $args, $and = '')
    {
        $postIds = implode(',', array_filter(Arr::get($args, 'post_ids', [])));
        $termIds = implode(',', array_filter(Arr::get($args, 'term_ids', [])));
        if (!empty($args['type'])) {
            $and.= "AND m2.meta_value = '{$args['type']}' ";
        }
        if ($postIds) {
            $and.= "AND m3.meta_key = '_assigned_to' AND m3.meta_value IN ({$postIds}) ";
        }
        if ($termIds) {
            $and.= "AND tr.term_taxonomy_id IN ({$termIds}) ";
        }
        return apply_filters('site-reviews/query/and-for-counts', $and);
    }

    /**
     * @param string $innerJoin
     * @return string
     */
    protected function getInnerJoinForCounts(array $args, $innerJoin = '')
    {
        if (!empty(Arr::get($args, 'post_ids'))) {
            $innerJoin.= "INNER JOIN {$this->db->postmeta} AS m3 ON p.ID = m3.post_id ";
        }
        if (!empty(Arr::get($args, 'term_ids'))) {
            $innerJoin.= "INNER JOIN {$this->db->term_relationships} AS tr ON p.ID = tr.object_id ";
        }
        return apply_filters('site-reviews/query/inner-join-for-counts', $innerJoin);
    }
}
