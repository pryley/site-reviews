<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
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
     * @param int $userId
     * @return int|false
     */
    public function assignUser($ratingId, $userId)
    {
        return $this->insertIgnore(glsr(Query::class)->getTable('assigned_users'), [
            'rating_id' => $ratingId,
            'user_id' => $userId,
        ]);
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
     * @return array
     */
    public function flatten(array $ratings = [], array $args = [])
    {
        $args = wp_parse_args($args, [
            'max' => glsr()->constant('MAX_RATING', Rating::class),
            'min' => glsr()->constant('MIN_RATING', Rating::class),
        ]);
        $counts = [];
        if (empty($ratings)) {
            $ratings = $this->ratings($args);
        }
        array_walk_recursive($ratings, function ($num, $index) use (&$counts) {
            $counts[$index] = $num + intval(Arr::get($counts, $index, 0));
        });
        foreach ($counts as $index => &$num) {
            if (!Helper::inRange($index, $args['min'], $args['max'])) {
                $num = 0;
            }
        }
        return $counts;
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
     * @param int $termId
     * @return int|false
     */
    public function unassignUser($ratingId, $userId)
    {
        return $this->db->delete(glsr(Query::class)->getTable('assigned_users'), [
            'rating_id' => $ratingId,
            'user_id' => $userId,
        ]);
    }

    /**
     * @param int $reviewId
     * @return int|bool
     */
    public function update($reviewId, array $data = [])
    {
        $data = array_intersect_key($data, $this->normalize($data));
        return $this->db->update(glsr(Query::class)->getTable('ratings'), $data, [
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
     * @param string $table
     * @return int|false
     */
    public function insertBulk($table, array $values, array $fields)
    {
        $this->db->insert_id = 0;
        $data = [];
        foreach ($values as $value) {
            $value = array_intersect_key($value, array_flip($fields)); // only keep field values
            if (count($value) === count($fields)) {
                $value = array_merge(array_flip($fields), $value); // make sure the order is correct
                $data[] = sprintf("('%s')", implode("','", array_values($value)));
            }
        }
        $table = glsr(Query::class)->getTable($table);
        $fields = implode('`,`', $fields);
        $values = implode(",", array_values($data));
        return $this->db->query(
            $this->db->prepare("INSERT IGNORE INTO {$table} (`{$fields}`) VALUES {$values}")
        );
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
        $data = glsr(RatingDefaults::class)->restrict($data);
        if (array_key_exists('is_approved', $data)) {
            $data['is_approved'] = Helper::castToBool($data['is_approved']);
        }
        if (array_key_exists('rating', $data)) {
            $data['rating'] = Helper::castToInt($data['rating']);
        }
        if (array_key_exists('review_id', $data)) {
            $data['review_id'] = Helper::castToInt($data['review_id']);
        }
        if (array_key_exists('type', $data)) {
            $data['type'] = sanitize_key($data['type']);
        }
        return $data;
    }
}
