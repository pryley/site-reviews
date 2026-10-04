<?php

namespace GeminiLabs\SiteReviews\Compat\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\FetchPagedReviews;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Request;

/**
 * The admin-ajax routes that the public script used before 8.4.0.
 */
class AjaxController extends AbstractController
{
    /**
     * @action site-reviews/route/ajax/approved-review
     */
    public function approvedReviewAjax(Request $request): void
    {
        $reviewId = $request->cast('review_id', 'int');
        $review = glsr_get_review($reviewId);
        if (!$review->isValid() || !$review->is_approved) {
            wp_send_json_error();
        }
        $html = $review->build($request->toArray());
        wp_send_json_success([
            'attributes' => $html->attributes(),
            'review' => (string) $html,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/fetch-paged-reviews
     */
    public function fetchPagedReviewsAjax(Request $request): void
    {
        $this->execute(new FetchPagedReviews($request))->sendJsonResponse();
    }

    /**
     * @action site-reviews/route/ajax/submit-review
     */
    public function submitReviewAjax(Request $request): void
    {
        $command = $this->execute(new CreateReview($request));
        $command->sendJsonResponse();
    }

    /**
     * @action site-reviews/route/ajax/verified-review
     */
    public function verifiedReviewAjax(Request $request): void
    {
        $reviewId = $request->cast('review_id', 'int');
        $token = sanitize_text_field($request->get('verified'));
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
                'message' => $message,
                'review' => (string) $html,
            ]);
        }
        wp_send_json_error();
    }
}
