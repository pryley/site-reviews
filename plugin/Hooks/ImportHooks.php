<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\ImportController;

class ImportHooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(ImportController::class, [
            ['filterReviewPostData', 'site-reviews/review/create/post_data', 10, 2],
        ]);
    }
}
