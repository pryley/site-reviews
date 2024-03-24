<?php

namespace GeminiLabs\SiteReviews\Integrations\LPFW;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Controller extends AbstractController
{
    /**
     * @param array $types
     *
     * @filter lpfw_get_point_earn_source_types
     */
    public function filterEarnPointTypes($types = []): array
    {
        $types = Arr::consolidate($types);
        $types['product_review'] = [
            'name' => _x('Leaving a product review', '(loyalty-program-for-woocommerce) admin-text', 'site-reviews'),
            'slug' => 'product_review',
            'related' => [
                'object_type' => glsr()->post_type,
                'admin_label' => _x('View Review', '(loyalty-program-for-woocommerce) admin-text', 'site-reviews'),
                'label' => _x('View Product', '(loyalty-program-for-woocommerce) admin-text', 'site-reviews'),
                'admin_link_callback' => 'get_edit_post_link',
                'link_callback' => [$this, 'productUrl'],
            ],
        ];
        return $types;
    }

    /**
     * @action site-reviews/review/approved
     */
    public function onApprovedReview(Review $review): void
    {
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        $this->maybeEarnPoints($review);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
        if ($review->is_approved) {
            $this->maybeEarnPoints($review);
        }
    }

    /**
     * @param int $reviewId
     *
     * @see $this->filterEarnPointTypes()
     */
    public function productUrl($reviewId): string
    {
        $review = glsr_get_review($reviewId);
        if (!$review->isValid()) {
            return '';
        }
        foreach ($review->assigned_posts as $postId) {
            if ('product' === get_post_type($postId) && 'publish' === get_post_status($postId)) {
                return (string) get_permalink($postId);
            }
        }
        return '';
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
            'status' => 'approved',
            'user__in' => $review->author_id,
        ]);
        return 1 >= $reviews->total;
    }

    protected function maybeEarnPoints(Review $review): void
    {
        if (!$review->author_id) {
            return;
        }
        foreach ($review->assigned_posts as $postId) {
            if ('product' !== get_post_type($postId)) {
                continue;
            }
            if ($this->isFirstReviewForPost($postId, $review)) {
                $this->earnPoints($review->ID, $review->author_id);
            }
        }
    }
}
