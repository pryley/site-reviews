<?php

namespace GeminiLabs\SiteReviews\Integrations\WooRewards;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @action site-reviews/review/approved
     */
    public function onApprovedReview(Review $review): void
    {
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        $this->processPoints($review);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        if ($review->is_approved) {
            $this->processPoints($review);
        }
    }

    protected function fakeComment(int $postId, Review $review): \WP_Comment
    {
        return new \WP_Comment((object) [ // @phpstan-ignore-line
            'comment_approved' => $review->is_approved,
            'comment_ID' => $review->ID,
            'comment_post_ID' => $postId,
            'comment_type' => 'review',
            'user_id' => $review->author_id,
        ]);
    }

    /**
     * This allows us to invoke a protected method.
     *
     * @return mixed
     */
    protected function invoke(string $method, array $args = [])
    {
        $event = glsr()->retrieve('\LWS\WOOREWARDS\Events\ProductReview');
        $fn = fn () => $this->$method(...$args);
        return $fn->bindTo($event, $event)();
    }

    protected function processPoints(Review $review): void
    {
        foreach ($review->assigned_posts as $postId) {
            if ('product' !== get_post_type($postId)) {
                continue;
            }
            try {
                $comment = $this->fakeComment($postId, $review);
                if ($this->invoke('isValid', [$comment, $delayed = false])) {
                    $this->invoke('process', [$comment]);
                }
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }
}
