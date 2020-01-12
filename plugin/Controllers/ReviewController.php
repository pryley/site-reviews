<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\CountsManager;
use GeminiLabs\SiteReviews\Database\GlobalCountsManager;
use GeminiLabs\SiteReviews\Database\PostCountsManager;
use GeminiLabs\SiteReviews\Database\TermCountsManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class ReviewController extends Controller
{
    /**
     * Triggered when a category is added to a review.
     *
     * @param int $postId
     * @param array $terms
     * @param array $newTTIds
     * @param string $taxonomy
     * @param bool $append
     * @param array $oldTTIds
     * @return void
     * @action set_object_terms
     */
    public function onAfterChangeCategory($postId, $terms, $newTTIds, $taxonomy, $append, $oldTTIds)
    {
        sort($newTTIds);
        sort($oldTTIds);
        if ($newTTIds === $oldTTIds || !$this->isReviewPostId($postId)) {
            return;
        }
        $review = glsr_get_review($postId);
        if ('publish' !== $review->status) {
            return;
        }
        $ignoredIds = array_intersect($oldTTIds, $newTTIds);
        $decreasedIds = array_diff($oldTTIds, $ignoredIds);
        $increasedIds = array_diff($newTTIds, $ignoredIds);
        if ($review->term_ids = glsr(Database::class)->getTermIds($decreasedIds, 'term_taxonomy_id')) {
            glsr(TermCountsManager::class)->decrease($review);
        }
        if ($review->term_ids = glsr(Database::class)->getTermIds($increasedIds, 'term_taxonomy_id')) {
            glsr(TermCountsManager::class)->increase($review);
        }
    }

    /**
     * Triggered when an existing review is approved|unapproved.
     *
     * @param string $oldStatus
     * @param string $newStatus
     * @param \WP_Post $post
     * @return void
     * @action transition_post_status
     */
    public function onAfterChangeStatus($newStatus, $oldStatus, $post)
    {
        if (Application::POST_TYPE != Arr::get($post, 'post_type') 
            || in_array($oldStatus, ['new', $newStatus])) {
            return;
        }
        $review = glsr_get_review($post);
        if ('publish' == $post->post_status) {
            glsr(CountsManager::class)->increaseAll($review);
        } else {
            glsr(CountsManager::class)->decreaseAll($review);
        }
    }

    /**
     * Triggered when a review is first created.
     *
     * @return void
     * @action site-reviews/review/created
     */
    public function onAfterCreate(Review $review)
    {
        if ('publish' !== $review->status) {
            return;
        }
        glsr(GlobalCountsManager::class)->increase($review);
        glsr(PostCountsManager::class)->increase($review);
    }

    /**
     * Triggered when a review is deleted.
     *
     * @param int $postId
     * @return void
     * @action before_delete_post
     */
    public function onBeforeDelete($postId)
    {
        if (!$this->isReviewPostId($postId)) {
            return;
        }
        $review = glsr_get_review($postId);
        if ('trash' !== $review->status) { // do not run for trashed posts
            glsr(CountsManager::class)->decreaseAll($review);
        }
    }

    /**
     * Triggered when a review's rating, assigned_to, or review_type is changed.
     *
     * @param int $metaId
     * @param int $postId
     * @param string $metaKey
     * @param mixed $metaValue
     * @return void
     * @action update_postmeta
     */
    public function onBeforeUpdate($metaId, $postId, $metaKey, $metaValue)
    {
        if (!$this->isReviewPostId($postId)) {
            return;
        }
        $metaKey = Str::removePrefix('_', $metaKey);
        if (!in_array($metaKey, ['assigned_to', 'rating', 'review_type'])) {
            return;
        }
        $review = glsr_get_review($postId);
        if ($review->$metaKey == $metaValue) {
            return;
        }
        $method = Helper::buildMethodName($metaKey, 'onBeforeChange');
        call_user_func([$this, $method], $review, $metaValue);
    }

    /**
     * Triggered by the onBeforeUpdate method.
     *
     * @param string|int $assignedTo
     * @return void
     */
    protected function onBeforeChangeAssignedTo(Review $review, $assignedTo)
    {
        glsr(PostCountsManager::class)->decrease($review);
        $review->assigned_to = $assignedTo;
        glsr(PostCountsManager::class)->increase($review);
    }

    /**
     * Triggered by the onBeforeUpdate method.
     *
     * @param string|int $rating
     * @return void
     */
    protected function onBeforeChangeRating(Review $review, $rating)
    {
        glsr(CountsManager::class)->decreaseAll($review);
        $review->rating = $rating;
        glsr(CountsManager::class)->increaseAll($review);
    }

    /**
     * Triggered by the onBeforeUpdate method.
     *
     * @param string $reviewType
     * @return void
     */
    protected function onBeforeChangeReviewType(Review $review, $reviewType)
    {
        glsr(CountsManager::class)->decreaseAll($review);
        $review->review_type = $reviewType;
        glsr(CountsManager::class)->increaseAll($review);
    }
}
