<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\SendVerificationEmail;
use GeminiLabs\SiteReviews\Commands\VerifyReview;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class VerificationController extends Controller
{
    /**
     * @action site-reviews/review/created
     */
    public function sendVerificationEmail(Review $review, CreateReview $command): void
    {
        $path = wp_parse_url((string) get_permalink($command->post_id), PHP_URL_PATH);
        $path = trailingslashit($path);
        $token = glsr(Encryption::class)->encryptRequest('verify', [$review->ID, $path]);
        if (!empty($token)) {
            $this->execute(new SendVerificationEmail($review, $token));
        }
    }

    /**
     * @action site-reviews/route/ajax/verified-review
     */
    public function verifiedReviewAjax(Request $request): void
    {
        $reviewId = $request->cast('post_id', 'int');
        $token = sanitize_text_field($request->get('token'));
        $token = (int) glsr(Encryption::class)->decrypt($token);
        if (empty($reviewId) || $reviewId !== $token) {
            wp_send_json_error();
        }
        $review = glsr_get_review($reviewId);
        if ($review->isValid()) {
            $html = $review->build($request->toArray());
            $message = $review->is_approved
                ? __('Thank you, your review has been verified.', 'site-reviews')
                : __('Thank you, your review has been verified and is awaiting approval.', 'site-reviews');
            wp_send_json_success([
                'attributes' => $html->attributes(),
                'review' => (string) $html,
                'message' => sprintf('<p style="background:rgb(240 253 244); border-top:1px solid rgba(22 101 52/.25); color:rgb(22 101 52); margin:0; padding:1em 1.5em;">%s</p>', $message),
            ]);
        }
        wp_send_json_error();
    }

    /**
     * @action site-reviews/route/get/public/verify
     */
    public function verifyReview(Request $request): void
    {
        $postId = Arr::getAs('int', $request->data, 0);
        $redirectUrl = get_home_url();
        $review = glsr_get_review($postId);
        if ($review->isValid() && $this->execute(new VerifyReview($review))) {
            $queryArgs = [
                'review_id' => $review->ID,
                'verified' => glsr(Encryption::class)->encrypt($review->ID),
            ];
            $path = Arr::get($request->data, 1);
            $redirectUrl = add_query_arg($queryArgs, $redirectUrl.$path);
        }
        wp_redirect($redirectUrl);
        exit;
    }
}
