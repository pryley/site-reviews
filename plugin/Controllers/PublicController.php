<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Contracts\BuilderContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class PublicController extends AbstractController
{
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
        glsr()->store(glsr()->paged_handle, $request->toArray());
        $html = glsr(SiteReviewsShortcode::class)
            ->normalize($request->cast('atts', 'array'))
            ->buildReviewsHtml();
        $response = [
            'pagination' => $html->getPagination($wrap = false),
            'reviews' => $html->getReviews(),
        ];
        wp_send_json_success($response);
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
        $command = $this->execute(new CreateReview($request));
        if ($command->successful()) {
            wp_safe_redirect($command->referer()); // @todo add review ID to referer?
            exit;
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
}
