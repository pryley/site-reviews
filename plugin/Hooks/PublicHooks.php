<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\PublicController;

class PublicHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(PublicController::class, [
            ['enqueueAssets', 'wp_enqueue_scripts', 999], // run after all addons
            ['filterRenderView', 'site-reviews/render/view'],
            ['renderSchema', 'wp_footer'],
            ['submitReview', 'site-reviews/route/public/submit-review'],
            ['submittedReview', 'site-reviews/route/get/public/submitted'],
        ]);
    }
}
