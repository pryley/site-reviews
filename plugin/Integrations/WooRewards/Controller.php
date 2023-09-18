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
    public $event;

    /**
     * @action site-reviews/review/approved
     */
    public function onApprovedReview(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        $this->processPoints($review);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        if ($review->is_approved) {
            $this->processPoints($review);
        }
    }

    /**
     * @return \WP_Comment
     */
    protected function fakeComment(int $postId, Review $review)
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
     * @return mixed
     */
    protected function invoke(string $method, array $args = [])
    {
        $fn = function () use ($method, $args) {
            return $this->$method(...$args);
        };
        return $fn->bindTo($this->event, $this->event)();
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
