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
            try {
                if ($this->isValid($postId, $review)) {
                    $this->earnPoints($postId, $review->author_id, $force = true);
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
            try {
                if ($this->isValid($postId, $review)) {
                    $this->earnPoints($postId, $review->author_id, $force = false);
                }
            } catch (\Exception $e) {
                glsr_log()->error($e->getMessage());
            }
        }
    }

    protected function earnPoints(int $postId, int $userId, bool $force): void
    {
        if (!$force && !$this->isCool($userId)) {
            return;
        }
        add_user_meta($userId, $this->oncekey(), $postId, false);
        $options = [
            'product' => $postId,
            'user' => $userId,
        ];
        $reason = \LWS\WOOREWARDS\Core\Trace::byReason([
            "Review about a product (%s)",
            get_the_title($postId),
        ]);
        $this->addPoint($options, $reason, 1);
    }

    protected function isFirstReviewForPost(int $postId, Review $review): bool
    {
        $reviews = glsr_get_reviews([
            'assigned_posts' => $postId,
            'status' => 'all',
            'user__in' => $review->author_id,
        ]);
        return 1 >= $reviews->total;
    }

    protected function isProductOrdered(int $postId, int $userId): bool
    {
        if (!$this->isFirstReviewForPost()) {
            return false;
        }
        if (!$this->event->isPurchaseRequired()) {
            return true;
        }
        global $wpdb;
        $sql = "
            SELECT count(*) FROM {$wpdb->posts} as p
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as m ON m.meta_key = '_product_id' AND m.meta_value = %d
            INNER JOIN {$wpdb->prefix}woocommerce_order_items as i ON m.order_item_id = i.order_item_id AND p.ID = i.order_id
            INNER JOIN {$wpdb->postmeta} as c ON c.post_id = p.ID AND c.meta_key = '_customer_user' AND c.meta_value = %d
            WHERE p.post_type = 'shop_order' AND p.post_status IN ('wc-completed', 'wc-processing', 'wc-refunded')
        ";
        return !empty(
            (int) $wpdb->get_var($wpdb->prepare($sql, $postId, $userId))
        );
    }

    protected function isValid(int $postId, Review $review): bool
    {
        if (!$review->author_id) {
            return false;
        }
        if ('product' !== get_post_type($postId)) {
            return false;
        }
        if (in_array($postId, get_user_meta($review->author_id, $this->onceKey(), false))) {
            return false;
        }
        if (!$this->isProductOrdered($postId, $review->author_id)) {
            return false;
        }
        return true;
    }

    protected function onceKey(): string
    {
        return 'lws_wre_event_review_'.$this->event->getId();
    }
}
