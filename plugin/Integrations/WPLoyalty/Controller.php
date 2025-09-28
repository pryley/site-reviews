<?php

namespace GeminiLabs\SiteReviews\Integrations\WPLoyalty;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Review;
use Wlr\App\Helpers\EarnCampaign;
use Wlr\App\Helpers\Woocommerce;
use Wlr\App\Premium\Helpers\ProductReview;
use Wlr\App\Premium\Helpers\Referral;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/review-form/fields/visible
     */
    public function filterFieldAfter(array $fields, ReviewForm $form): array
    {
        $assignedPostId = Cast::toInt($form->args->assigned_posts);
        if (empty($assignedPostId)) {
            return $fields;
        }
        if (!$product = Woocommerce::getInstance()->getProduct($assignedPostId)) {
            return $fields;
        }
        if (!is_user_logged_in() || Woocommerce::getInstance()->isBannedUser()) {
            return $fields;
        }
        if (apply_filters('wlr_hide_product_review_message', false, [])) {
            return $fields;
        }
        foreach ($fields as $field) {
            if ('content' !== $field->original_name) {
                continue;
            }
            $rewardsList = EarnCampaign::getInstance()->getActionEarning(['product_review'], [
                'is_calculate_based' => 'product',
                'product' => $product,
                'product_id' => $assignedPostId,
                'user_email' => Woocommerce::getInstance()->get_login_user_email(),
            ]);
            $messages = [$field->after];
            foreach ($rewardsList as $action => $rewards) {
                foreach ($rewards as $key => $reward) {
                    $messages[] = $reward['messages'] ?? '';
                }
            }
            $field->after = implode('<br/>', array_filter($messages));
            break;
        }
        return $fields;
    }

    /**
     * @action site-reviews/review/approved
     */
    public function onApprovedReview(Review $review): void
    {
        $review->refresh();
        $this->maybeEarnPoints($review);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onCreatedReview(Review $review): void
    {
        if (!is_user_logged_in()) {
            return;
        }
        $review->refresh();
        if (!$review->is_approved) {
            return;
        }
        $this->maybeEarnPoints($review);
    }

    protected function earnPoints(string $customerEmail, int $productId): void
    {
        $data = [
            'is_calculate_based' => 'product',
            'product' => Woocommerce::getInstance()->getProduct($productId),
            'product_id' => $productId,
            'user_email' => $customerEmail,
        ];
        (new ProductReview())->applyEarnProductReview($data);
        (new Referral())->doReferralCheck($data);
    }

    protected function maybeEarnPoints(Review $review): void
    {
        if (!$user = $review->user()) {
            return;
        }
        if (Woocommerce::getInstance()->isBannedUser($user->user_email)) {
            return;
        }
        foreach ($review->assigned_posts as $postId) {
            if (Woocommerce::getInstance()->getProduct((int) $postId)) {
                $this->earnPoints($user->user_email, (int) $postId);
            }
        }
    }
}
