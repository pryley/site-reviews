<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ReviewManager
{
    /**
     * @param int $postId
     * @return int|false
     */
    public function assignPost(Review $review, $postId)
    {
        $where = [
            'is_published' => 'publish' === get_post_status($postId),
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->insert('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->posts($postId);
        }
        return $result;
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function assignTerm(Review $review, $termId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->terms($termId);
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return int|false
     */
    public function assignUser(Review $review, $userId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->users($userId);
        }
        return $result;
    }

    /**
     * @return false|Review
     */
    public function create(CreateReview $command)
    {
        if ($postId = $this->createRaw($command)) {
            $review = $this->get($postId);
            if ($review->isValid()) {
                glsr()->action('review/created', $review, $command);
                return $this->get($review->ID); // return a fresh copy of the review
            }
        }
        return false;
    }

    /**
     * @return false|int
     */
    public function createRaw(CreateReview $command)
    {
        $values = glsr()->args($command->toArray()); // this filters the values
        $postValues = [
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => $values->content,
            'post_date' => $values->date,
            'post_date_gmt' => $values->date_gmt,
            'post_name' => uniqid($values->type),
            'post_status' => $this->postStatus($values->type, $values->blacklisted),
            'post_title' => $values->title,
            'post_type' => glsr()->post_type,
        ];
        $postId = wp_insert_post($postValues, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($postValues);
            return false;
        }
        glsr()->action('review/create', $postId, $command);
        return $postId;
    }

    /**
     * @param int $reviewId
     * @return int|false
     */
    public function delete($reviewId)
    {
        glsr(Cache::class)->delete($reviewId, 'reviews');
        return glsr(Database::class)->delete('ratings', [
            'review_id' => $reviewId,
        ]);
    }

    /**
     * @param int $reviewId
     * @return void
     */
    public function deleteRevisions($reviewId)
    {
        $revisionIds = glsr(Query::class)->revisionIds($reviewId);
        foreach ($revisionIds as $revisionId) {
            wp_delete_post_revision($revisionId);
        }
    }

    /**
     * @param int $reviewId
     * @return Review
     */
    public function get($reviewId)
    {
        $review = glsr(Query::class)->review($reviewId);
        glsr()->action('get/review', $review, $reviewId);
        return $review;
    }

    /**
     * @return Reviews
     */
    public function reviews(array $args = [])
    {
        $args = (new NormalizePaginationArgs($args))->toArray();
        $results = glsr(Query::class)->reviews($args);
        $total = $this->total($args, $results);
        $reviews = new Reviews($results, $total, $args);
        glsr()->action('get/reviews', $reviews, $args);
        return $reviews;
    }

    /**
     * @return int
     */
    public function total(array $args = [], array $reviews = [])
    {
        return glsr(Query::class)->totalReviews($args, $reviews);
    }

    /**
     * @param int $postId
     * @return int|false
     */
    public function unassignPost(Review $review, $postId)
    {
        $where = [
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->delete('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->posts($postId);
        }
        return $result;
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function unassignTerm(Review $review, $termId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->terms($termId);
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return int|false
     */
    public function unassignUser(Review $review, $userId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->users($userId);
        }
        return $result;
    }

    /**
     * @param int $reviewId
     * @return int|bool
     */
    public function update($reviewId, array $data = [])
    {
        glsr(Cache::class)->delete($reviewId, 'reviews');
        $defaults = glsr(RatingDefaults::class)->restrict($data);
        if ($data = array_intersect_key($data, $defaults)) {
            return glsr(Database::class)->update('ratings', $data, [
                'review_id' => $reviewId,
            ]);
        }
        return 0;
    }

    /**
     * @param int $reviewId
     * @return void
     */
    public function updateCustom($reviewId, array $data = [])
    {
        $fields = glsr()->config('forms/metabox-fields');
        $defaults = glsr(RatingDefaults::class)->defaults();
        $customKeys = array_keys(array_diff_key($fields, $defaults));
        if ($data = shortcode_atts(array_fill_keys($customKeys, ''), $data)) {
            $data = Arr::prefixKeys($data, 'custom_');
            foreach ($data as $metaKey => $metaValue) {
                glsr(Database::class)->metaSet($reviewId, $metaKey, $metaValue);
            }
        }
    }

    /**
     * @param int $postId
     * @param bool $isPublished
     * @return int|bool
     */
    public function updateAssignedPost($postId, $isPublished)
    {
        $isPublished = wp_validate_boolean($isPublished);
        $postId = Cast::toInt($postId);
        return glsr(Database::class)->update('assigned_posts',
            ['is_published' => $isPublished],
            ['post_id' => $postId]
        );
    }

    /**
     * @param string $reviewType
     * @param bool $isBlacklisted
     * @return string
     */
    protected function postStatus($reviewType, $isBlacklisted)
    {
        $requireApproval = glsr(OptionManager::class)->getBool('settings.general.require.approval');
        return 'local' == $reviewType && ($requireApproval || $isBlacklisted)
            ? 'pending'
            : 'publish';
    }
}
