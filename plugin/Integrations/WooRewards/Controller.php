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
        $this->processPoints($review, $force = true);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        $this->processPoints($review, $force = false);
    }

    /**
     * @return \WP_Comment
     */
    protected function fakeComment(int $postId,  Review $review)
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
     * @return mixed
     */
    protected function invoke(string $method, array $args = [])
    {
        $fn = function () use ($method, $args) {
            return $this->$method(...$args);
        };
        return $fn->bindTo($this->event, $this->event)();
    }

    protected function processPoints(Review $review, bool $force = false): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        foreach ($review->assigned_posts as $postId) {
            try {
                $comment = $this->fakeComment($postId, $review);
                if (!$this->invoke('isValid', [$comment, $delayed = false])) {
                    continue;
                }
                $this->invoke('process', [$comment, $force]);
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }
}
