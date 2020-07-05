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
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->insertRaw(glsr(Query::class)->table('assigned_posts'), [
            'is_published' => 'publish' === get_post_status($postId),
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ]);
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function assignTerm(Review $review, $termId)
    {
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->insertRaw(glsr(Query::class)->table('assigned_terms'), [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ]);
    }

    /**
     * @param int $userId
     * @return int|false
     */
    public function assignUser(Review $review, $userId)
    {
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->insertRaw(glsr(Query::class)->table('assigned_users'), [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ]);
    }

    /**
     * @return false|Review
     */
    public function create(CreateReview $command)
    {
        $postValues = [
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => $command->content,
            'post_date' => $command->date,
            'post_date_gmt' => get_gmt_from_date($command->date),
            'post_name' => uniqid($command->type),
            'post_status' => $this->postStatus($command->type, $command->blacklisted),
            'post_title' => $command->title,
            'post_type' => glsr()->post_type,
        ];
        $postId = wp_insert_post($postValues, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($postValues);
            return false;
        }
        glsr()->action('review/create', $postId, $command);
        $review = $this->get($postId);
        if ($review->isValid()) {
            glsr()->action('review/created', $review, $command);
            return $review;
        }
        return false;
    }

    /**
     * @param int $reviewId
     * @return void
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
        $reviews = glsr(Query::class)->reviews($args);
        $total = $this->total($args, $reviews);
        glsr()->action('get/reviews', $reviews, $args);
        return new Reviews($reviews, $total, $args);
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
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->delete(glsr(Query::class)->table('assigned_posts'), [
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ]);
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function unassignTerm(Review $review, $termId)
    {
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->delete(glsr(Query::class)->table('assigned_terms'), [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ]);
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function unassignUser(Review $review, $userId)
    {
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return glsr(Database::class)->delete(glsr(Query::class)->table('assigned_users'), [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ]);
    }

    /**
     * @param int $reviewId
     * @return int|bool
     */
    public function update($reviewId, array $data = [])
    {
        $defaults = glsr(RatingDefaults::class)->restrict($data);
        if ($data = array_intersect_key($data, $defaults)) {
            glsr(Cache::class)->delete($reviewId, 'reviews');
            return glsr(Database::class)->update('ratings', $data, [
                'review_id' => $reviewId,
            ]);
        }
        return 0;
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
            ['post_id' => $postId],
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
