<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\PublicController;

class PublicHooks extends AbstractHooks
{
    /**
     * @return void
     */
    public function run()
    {
        $this->hook(PublicController::class, [
            ['enqueueAssets', 'wp_enqueue_scripts', 999],
            ['fetchPagedReviewsAjax', 'site-reviews/route/ajax/fetch-paged-reviews'],
            ['filterEnqueuedScriptTags', 'script_loader_tag', 10, 2],
            ['filterFieldOrder', 'site-reviews/config/forms/review-form', 11],
            ['filterRenderView', 'site-reviews/render/view'],
            ['modifyBuilder', 'site-reviews/builder'],
            ['renderModal', 'wp_footer', 50],
            ['renderSchema', 'wp_footer'],
            ['submitReview', 'site-reviews/route/public/submit-review'],
            ['submitReviewAjax', 'site-reviews/route/ajax/submit-review'],
        ]);
    }
}
