<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\PublicController;

class PublicHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(PublicController::class, [
            ['enqueueAssets', 'wp_enqueue_scripts', 999], // run after all addons
            ['fetchPagedReviewsAjax', 'site-reviews/route/ajax/fetch-paged-reviews'],
            ['filterEnqueuedScriptTags', 'script_loader_tag', 10, 3],
            ['filterFieldOrder', 'site-reviews/config/forms/review-form', 11],
            ['filterRenderView', 'site-reviews/render/view'],
            ['modifyBuilder', 'site-reviews/builder'],
            ['renderSchema', 'wp_footer'],
            ['submitReview', 'site-reviews/route/public/submit-review'],
            ['submitReviewAjax', 'site-reviews/route/ajax/submit-review'],
        ]);
    }
}
