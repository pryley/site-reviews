<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;

class RatingManager
{
    protected $db;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @param int $ratingId
     * @param int $postId
     * @return int|false
     */
    public function assignPost($ratingId, $postId)
    {
        return $this->insertIgnore(glsr(Query::class)->getTable('assigned_posts'), [
            'is_published' => 'publish' === get_post_status($postId),
            'post_id' => $postId,
            'rating_id' => $ratingId,
        ]);
    }

    /**
     * @param int $ratingId
     * @return void
     */
    public function assignPosts($ratingId, array $postIds)
    {
        foreach ($postIds as $postId) {
            $this->assignPost($ratingId, $postId);
        }
    }

    /**
     * @param int $ratingId
     * @param int $termId
     * @return int|false
     */
    public function assignTerm($ratingId, $termId)
    {
        return $this->insertIgnore(glsr(Query::class)->getTable('assigned_terms'), [
            'rating_id' => $ratingId,
            'term_id' => $termId,
        ]);
    }

    /**
     * @param int $ratingId
     * @return int|false
     */
    public function assignTerms($ratingId, array $termIds)
    {
        foreach ($termIds as $termId) {
            $this->assignTerm($ratingId, $termId);
        }
    }

    /**
     * @param int $reviewId
     * @return int|false
     */
    public function delete($reviewId)
    {
        return $this->db->delete(glsr(Query::class)->getTable('ratings'), [
            'review_id' => $reviewId,
        ]);
    }

    /**
     * @param int $reviewId
     * @return object|false
     */
    public function get($reviewId)
    {
        return glsr(Query::class)->rating($reviewId);
    }

    /**
     * @param int $reviewId
     * @return object|false
     */
    public function insert($reviewId, array $data = [])
    {
        $data = Arr::set($this->normalize($data), 'review_id', $reviewId);
        $result = $this->insertIgnore(glsr(Query::class)->getTable('ratings'), $data);
        return (false !== $result)
            ? $this->get($reviewId)
            : false;
    }

    /**
     * @return array
     */
    public function ratings(array $args = [])
    {
        $ratings = glsr(Query::class)->ratings($args);
        foreach ($ratings as $type => $results) {
            $counts = $this->generateEmptyCountsArray();
            foreach ($results as $result) {
                $counts = Arr::set($counts, $result['rating'], $result['count']);
            }
            $ratings[$type] = $counts;
        }
        return $ratings;
    }

    /**
     * @param int $ratingId
     * @param int $postId
     * @return int|false
     */
    public function unassignPost($ratingId, $postId)
    {
        return $this->db->delete(glsr(Query::class)->getTable('assigned_posts'), [
            'post_id' => $postId,
            'rating_id' => $ratingId,
        ]);
    }

    /**
     * @param int $ratingId
     * @return void
     */
    public function unassignPosts($ratingId, array $postIds)
    {
        foreach ($postIds as $postId) {
            $this->unassignPost($ratingId, $postId);
        }
    }

    /**
     * @param int $ratingId
     * @param int $termId
     * @return int|false
     */
    public function unassignTerm($ratingId, $termId)
    {
        return $this->db->delete(glsr(Query::class)->getTable('assigned_terms'), [
            'rating_id' => $ratingId,
            'term_id' => $termId,
        ]);
    }

    /**
     * @param int $ratingId
     * @return int|false
     */
    public function unassignTerms($ratingId, array $termIds)
    {
        foreach ($termIds as $termId) {
            $this->unassignTerm($ratingId, $termId);
        }
    }

    /**
     * @param int $reviewId
     * @return int|bool
     */
    public function update($reviewId, array $data = [])
    {
        return $this->db->update(glsr(Query::class)->getTable('ratings'), $this->normalize($data), [
            'review_id' => $reviewId,
        ]);
    }

    /**
     * @return array
     */
    protected function generateEmptyCountsArray()
    {
        return array_fill_keys(range(0, glsr()->constant('MAX_RATING', Rating::class)), 0);
    }

    /**
     * @param string $tabel
     * @return int|false
     */
    protected function insertIgnore($table, array $data)
    {
        $this->db->insert_id = 0;
        $fields = implode('`,`', array_keys($data));
        $values = implode("','", array_values($data));
        return $this->db->query(
            $this->db->prepare("INSERT IGNORE INTO {$table} (`{$fields}`) VALUES ('{$values}')")
        );
    }

    /**
     * @return array
     */
    protected function normalize(array $data)
    {
        $defaults = [
            'is_approved' => '',
            'rating' => '',
            'type' => '',
        ];
        $data = shortcode_atts($defaults, $data);
        if (array_key_exists('is_approved', $data)) {
            $data['is_approved'] = Helper::castToBool($data['is_approved']);
        }
        if (array_key_exists('rating', $data)) {
            $data['rating'] = Helper::castToInt($data['rating']);
        }
        if (array_key_exists('type', $data)) {
            $data['type'] = sanitize_key($data['type']);
        }
        return $data;
    }
}
