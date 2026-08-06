<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Request;

/**
 * WooCommerce's "Customer review request" feature (customer_review_request,
 * WooCommerce 10.8+) emails customers after order completion and collects
 * reviews as comments on its own Review Order page, outside the plugin's
 * pipeline. Each submitted comment is converted into a plugin review, and
 * order items the customer has already reviewed with the plugin are excluded
 * from the feature's eligibility checks.
 */
class OrderReviewsController extends AbstractController
{
    /**
     * The comment is kept and marked as imported so that the batch importer
     * (see ImportProductReviews) will not import it a second time. A comment
     * that is already marked was converted by an earlier submission and has
     * only been edited in place by WooCommerce.
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
     * WooCommerce applies this filter on all of the feature's entry points
     * (the email decision, the Review Order page, and the submission handler),
     * so an excluded item is neither asked for nor accepted.
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
            // the "verified" state is recomputed against the real order by
            // verifyProductOwner on the "review/created" action
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
