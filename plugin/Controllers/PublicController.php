<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Commands\FetchPagedReviews;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Request;

class PublicController extends AbstractController
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
     * @action wp_enqueue_scripts
     */
    public function enqueueAssets(): void
    {
        $this->execute(new EnqueuePublicAssets());
    }

    /**
     * @action site-reviews/route/ajax/fetch-paged-reviews
     */
    public function fetchPagedReviewsAjax(Request $request): void
    {
        $this->execute(new FetchPagedReviews($request))->sendJsonResponse();
    }

    /**
     * @filter site-reviews/render/view
     */
    public function filterRenderView(string $view): string
    {
        return glsr(Style::class)->view($view);
    }

    /**
     * @action wp_footer
     */
    public function renderSchema(): void
    {
        if (empty(glsr_get_option('schema.integration.plugin'))) {
            glsr(Schema::class)->render();
        }
    }

    /**
     * @action site-reviews/route/public/submit-review
     */
    public function submitReview(Request $request): void
    {
        $command = new CreateReview($request);
        $this->execute($command);
        if ($command->successful()) {
            $redirect = $command->referer();
            $review = $command->review();
            if ($review->isValid()) {
                // carry the success message across the redirect for the no-JS form
                $token = glsr(Encryption::class)->encryptRequest('submitted', [$review->ID, time()]);
                if (!empty($token)) {
                    $redirect = add_query_arg(glsr()->prefix, $token, $redirect);
                }
            }
            wp_safe_redirect($redirect);
            glsr_exit();
        }
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
     * A no-JS form submission redirects back with an encrypted token in the URL.
     * This stores the success message in the session before the form renders.
     * The token expires so that a shared or cached URL does not replay the message.
     *
     * @action site-reviews/route/get/public/submitted
     */
    public function submittedReview(Request $request): void
    {
        $review = glsr_get_review(Arr::getAs('int', $request->data, 0));
        if (!$review->isValid()) {
            return;
        }
        if (time() - Arr::getAs('int', $request->data, 1) > 5 * \MINUTE_IN_SECONDS) {
            return;
        }
        $message = $review->is_approved
            ? __('Your review has been submitted!', 'site-reviews')
            : __('Your review has been submitted and is pending approval.', 'site-reviews');
        glsr()->sessionSet('form_message', $message);
        glsr()->sessionSet('form_success', true);
    }
}
