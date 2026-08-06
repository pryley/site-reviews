<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;

/**
 * WooCommerce's "Customer review request" feature (10.8+) collects reviews
 * as comments on its own Review Order page, outside the plugin's pipeline.
 */
class OrderReviewsController extends AbstractController
{
    /**
     * WooCommerce keeps the comment as its own record; the "imported" mark
     * makes the batch importer skip it. A marked comment is an edit.
     *
     * @param \WC_Order $order
     *
     * @action woocommerce_review_order_submitted
     */
    public function convertSubmittedReviews($order, array $results): void
    {
        foreach ($results as $result) {
            $commentId = Arr::getAs('int', $result, 'comment_id');
            if (0 < $commentId) {
                $this->convertComment($commentId);
            }
        }
    }

    /**
     * WooCommerce applies this filter on all of the feature's entry points:
     * an excluded item is not requested and not accepted.
     *
     * @param mixed     $items
     * @param \WC_Order $order
     *
     * @filter woocommerce_review_order_eligible_items
     */
    public function filterEligibleItems($items, $order): array
    {
        $items = Arr::consolidate($items);
        foreach ($items as $key => $item) {
            if (!$item instanceof \WC_Order_Item_Product) {
                continue;
            }
            if ($this->hasReviewFor((int) $item->get_product_id(), $order)) {
                unset($items[$key]);
            }
        }
        return $items;
    }

    protected function convertComment(int $commentId): void
    {
        if ('' !== (string) get_comment_meta($commentId, 'imported', true)) {
            return;
        }
        $comment = get_comment($commentId);
        if (!$comment instanceof \WP_Comment) {
            return;
        }
        $values = [
            'assigned_posts' => $comment->comment_post_ID,
            'author_id' => $comment->user_id,
            'content' => $comment->comment_content,
            'date' => $comment->comment_date,
            'date_gmt' => $comment->comment_date_gmt,
            'email' => $comment->comment_author_email,
            'ip_address' => $comment->comment_author_IP,
            'is_approved' => $comment->comment_approved,
            'name' => $comment->comment_author,
            'rating' => get_comment_meta($commentId, 'rating', true),
        ];
        $values = array_map('trim', $values);
        $command = new CreateReview(new Request($values));
        if (glsr(ReviewManager::class)->create($command)) {
            // verifyProductOwner recomputes "verified" on "review/created"
            update_comment_meta($commentId, 'imported', 1);
        }
    }

    protected function hasReviewFor(int $productId, \WC_Order $order): bool
    {
        if ($productId < 1) {
            return false;
        }
        $args = [
            'assigned_posts' => $productId,
            'per_page' => 1,
            'status' => 'all',
        ];
        $email = $order->get_billing_email();
        if (!empty($email) && 0 < glsr_get_reviews(array_merge($args, ['email' => $email]))->total) {
            return true;
        }
        $userId = (int) $order->get_customer_id();
        if (0 < $userId && 0 < glsr_get_reviews(array_merge($args, ['author_id' => $userId]))->total) {
            return true;
        }
        return false;
    }
}
