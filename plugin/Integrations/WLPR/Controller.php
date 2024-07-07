<?php

namespace GeminiLabs\SiteReviews\Integrations\WLPR;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;
use Wlpr\App\Helpers\Loyalty;
use Wlpr\App\Models\PointAction;

class Controller extends AbstractController
{
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

    protected function earnPoints(string $email, int $productId): void
    {
        $settings = get_option('wlpr_settings');
        $points = Arr::getAs('int', $settings, 'wlpr_write_review_points', 50);
        if ($points < 1) {
            return;
        }
        $pointAction = new PointAction();
        if (!method_exists(Loyalty::class, 'point') || !method_exists($pointAction, 'getWhere')) { // @phpstan-ignore-line
            glsr_log()->error('The "WooCommerce Loyalty Points and Rewards" integration is broken and needs an update.');
            return;
        }
        global $wpdb;
        $sql = "user_email = %s AND action = 'product-review' AND product_id = %d";
        if (empty($pointAction->getWhere($wpdb->prepare($sql, $email, $productId), '*', true))) {
            $wlpr = Loyalty::point();
            $wlpr->addEarnPoint($email, $points, 'product-review', [
                'product_id' => $productId,
            ]);
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
        foreach ($review->assigned_posts as $postId) {
            if ('product' !== get_post_type($postId)) {
                continue;
            }
            if ($user = get_user_by('id', $review->author_id)) {
                if ($this->isFirstReviewForPost($postId, $review)) {
                    $this->earnPoints(sanitize_email($user->user_email), $postId);
                }
            }
        }
    }
}
