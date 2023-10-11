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
     * @action site-reviews/route/get/public/verify
     */
    public function verifyReview(Request $request): void
    {
        $postId = Arr::get($request->data, 0);
        $redirectUrl = get_home_url();
        $review = glsr_get_review($postId);
        if ($review->isValid()) {
            $queryArgs = [
                'review_id' => $review->ID,
                'verified' => $this->execute(new VerifyReview($review)),
            ];
            $path = Arr::get($request->data, 1);
            $redirectUrl = add_query_arg($queryArgs, $redirectUrl.$path);
        }
        wp_redirect($redirectUrl);
        exit;
    }
}
