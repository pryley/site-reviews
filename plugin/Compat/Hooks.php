<?php

namespace GeminiLabs\SiteReviews\Compat;

use GeminiLabs\SiteReviews\Compat\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Compat\Controllers\Controller;
use GeminiLabs\SiteReviews\Compat\Controllers\DeprecationController;
use GeminiLabs\SiteReviews\Compat\Controllers\HookController;
use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

class Hooks extends AbstractHooks
{
    public function levelPluginsLoaded(): ?int
    {
        return 10;
    }

    /**
     * @action plugins_loaded
     */
    public function onPluginsLoaded(): void
    {
        if (glsr()->filterBool('support/deprecated/v8', true)) {
            $this->hook(HookController::class, [
                ['filterReviewFormOrder', 'site-reviews/review-form/fields/order'],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v7', true)) {
            $this->hook(HookController::class, [
                ['filterReviewFormFieldsNormalized', 'site-reviews/review-form/fields/all', 10, 2],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v6', true)) {
            $this->hook(HookController::class, [
                ['filterRestApiReviewsProperties', 'site-reviews/rest-api/reviews/schema/properties', 9],
                ['filterRestApiSummaryProperties', 'site-reviews/rest-api/summary/schema/properties', 9],
                ['filterSlackCompose', 'site-reviews/slack/notification'],
                ['reviewSaved', 'site-reviews/review/updated', 10, 2],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v5', true)) {
            $this->hook(HookController::class, [
                ['filterAssignedToTag', 'site-reviews/build/template/review'],
                ['filterReviewIdTag', 'site-reviews/build/template/review'],
                ['filterReviewTableFilter', 'site-reviews/defaults/review-table-filters'],
                ['filterReviewsWrapper', 'site-reviews/rendered/template/reviews'],
                ['filterSubmissionFormConfig', 'site-reviews/config/forms/review-form', 9],
                ['filterSubmissionFormOrder', 'site-reviews/review-form/fields/order', 9],
                ['reviewResponse', 'site-reviews/review/responded', 9, 2],
            ]);
        }
    }

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
        $this->hook(DeprecationController::class, [
            ['logNotices', 'admin_footer'],
            ['logNotices', 'wp_footer'],
            ['storeFunctionNotice', 'deprecated_function_run', 10, 3],
            ['storeHookNotice', 'deprecated_hook_run', 10, 4],
        ]);
    }
}
