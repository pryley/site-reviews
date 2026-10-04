<?php

namespace GeminiLabs\SiteReviews\Compat;

use GeminiLabs\SiteReviews\Compat\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Compat\Controllers\Controller;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(AjaxController::class, [
            ['approvedReviewAjax', 'site-reviews/route/ajax/approved-review'],
            ['fetchPagedReviewsAjax', 'site-reviews/route/ajax/fetch-paged-reviews'],
            ['submitReviewAjax', 'site-reviews/route/ajax/submit-review'],
            ['verifiedReviewAjax', 'site-reviews/route/ajax/verified-review'],
        ]);
        $this->hook(Controller::class, [
            ['enqueueCanvasScript', 'enqueue_block_assets', 999],
            ['requireCompatScript', 'admin_print_footer_scripts', 0],
            ['requireCompatScript', 'wp_print_footer_scripts', 0],
            ['requireCompatScript', 'wp_print_scripts', 0],
        ]);
    }
}
