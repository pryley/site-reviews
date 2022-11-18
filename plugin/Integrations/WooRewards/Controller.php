<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @var \LWS\WOOREWARDS\Events\ProductReview|null
     */
    public $event = null;

    /**
     * @action site-reviews/review/approved
     */
    public function onApprovedReview(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        foreach ($review->assigned_posts as $postId) {
            if (!$this->isFirstReviewForPost($postId, $review)) {
                continue;
            }
            try {
                $comment = $this->fakeComment($postId, $review);
                if ($this->invoke('isValid', [$comment, $delayed = false])) {
                    $this->invoke('process', [$comment, $force = true]);
                }
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        foreach ($review->assigned_posts as $postId) {
            if (!$this->isFirstReviewForPost($postId, $review)) {
                continue;
            }
            try {
                $comment = $this->fakeComment($postId, $review);
                if ($this->invoke('isValid', [$comment, $delayed = false])) {
                    $this->invoke('process', [$comment, $force = false]);
                }
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    /**
     * @return \WP_Comment
     */
    protected function fakeComment(int $postId,  Review $review)
    {
        return new \WP_Comment((object) [
            'comment_approved' => $review->is_approved,
            'comment_ID' => $review->ID,
            'comment_post_ID' => $postId,
            'comment_type' => 'review',
            'user_id' => $review->author_id,
        ]);
    }

    /**
     * @return mixed
     */
    protected function invoke(string $method, array $args = [])
    {
        $fn = function () use ($method, $args) {
            return $this->$method(...$args);
        };
        return $fn->bindTo($this->event, $this->event)();
    }
}
