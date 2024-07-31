<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\SendVerificationEmail;
use GeminiLabs\SiteReviews\Commands\ToggleVerified;
use GeminiLabs\SiteReviews\Commands\VerifyReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class VerificationController extends AbstractController
{
    /**
     * @action post_submitbox_misc_actions
     */
    public function renderVerifyAction(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $review = glsr(ReviewManager::class)->get($post->ID);
        if (!$review->isValid()) {
            return;
        }
        $text = empty(glsr(Database::class)->meta($review->ID, 'verified_requested'))
            ? esc_html_x('Send Verification Request', 'admin-text', 'site-reviews')
            : esc_html_x('Resend Verification Request', 'admin-text', 'site-reviews');
        glsr(Template::class)->render('partials/editor/verified', [
            'is_verification_enabled' => glsr(OptionManager::class)->getBool('settings.general.request_verification', false),
            'is_verified' => $review->is_verified,
            'text' => $text,
        ]);
    }

    /**
     * @action site-reviews/route/ajax/request-verification
     */
    public function resendVerificationEmailAjax(Request $request): void
    {
        $review = glsr(ReviewManager::class)->get($request->cast('post_id', 'int'));
        $pathPostId = $review->meta()->cast('_submitted._post_id', 'int');
        $path = wp_parse_url((string) get_permalink($pathPostId), PHP_URL_PATH);
        $command = $this->execute(new SendVerificationEmail($review, $review->verifyUrl($path)));
        wp_send_json_success($command->response());
    }

    /**
     * @action site-reviews/review/created
     */
    public function sendVerificationEmail(Review $review, CreateReview $command): void
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (!in_array($review->status, ['pending', 'publish'])) {
            return; // this review is likely a draft made in the wp-admin
        }
        $path = wp_parse_url((string) get_permalink($command->post_id), PHP_URL_PATH);
        $verifyUrl = $review->verifyUrl($path);
        if (!empty($verifyUrl)) {
            $this->execute(new SendVerificationEmail($review, $verifyUrl));
        }
    }

    /**
     * @action site-reviews/route/ajax/toggle-verified
     */
    public function toggleVerifiedAjax(Request $request): void
    {
        $command = $this->execute(new ToggleVerified($request));
        glsr()->action('cache/flush', $command->review); // @phpstan-ignore-line
        wp_send_json_success($command->response());
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

    /**
     * @action site-reviews/route/get/public/verify
     */
    public function verifyReview(Request $request): void
    {
        $postId = Arr::getAs('int', $request->data, 0);
        $redirectUrl = get_home_url();
        $review = glsr_get_review($postId);
        if ($review->isValid()) {
            $command = $this->execute(new VerifyReview($review));
            $path = Arr::get($request->data, 1);
            $redirectUrl .= $path;
            $redirectUrl = add_query_arg('review_id', $review->ID, $redirectUrl);
            if ($command->successful()) {
                glsr()->action('cache/flush', $review);
                $token = glsr(Encryption::class)->encrypt($review->ID);
                $redirectUrl = add_query_arg('verified', $token, $redirectUrl);
            }
        }
        wp_redirect($redirectUrl);
        exit;
    }
}
