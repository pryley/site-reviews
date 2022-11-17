<?php

namespace GeminiLabs\SiteReviews\Integrations\LPFW;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    /**
     * @action site-reviews/review/created
     * @action site-reviews/review/approved
     */
    public function maybeEarnPoints(Review $review): void
    {
        if (!$review->author_id) {
            return;
        }
        foreach ($review->assigned_posts as $postId) {
            if ($this->isFirstReviewForPost($postId, $review)) {
                $this->earnPoints($review->ID, $review->author_id);
            }
        }
    }

    protected function earnPoints(int $reviewId, int $userId): void
    {
        if (!\LPFW()->Earn_Points->should_customer_earn_points($userId) 
            || get_post_meta($reviewId, \LPFW()->Plugin_Constants->COMMENT_ENTRY_ID_META, true)) {
            return;
        }
        $points = (int) \LPFW()->Helper_Functions->get_option(\LPFW()->Plugin_Constants->EARN_POINTS_PRODUCT_REVIEW);
        if ($points) {
            $entryId = \LPFW()->Entries->increase_points($userId, $points, 'product_review', $reviewId);
            update_post_meta($reviewId, \LPFW()->Plugin_Constants->COMMENT_ENTRY_ID_META, $entryId);
        }
    }

    protected function isFirstReviewForPost(int $postId, Review $review): bool
    {
        $reviews = glsr_get_reviews([
            'assigned_posts' => $postId,
            'status' => 'all',
            'user__in' => $review->author_id,
        ]);
        return 1 <= $reviews->total;
    }
}
