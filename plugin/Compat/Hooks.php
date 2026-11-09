<?php

namespace GeminiLabs\SiteReviews\Compat;

use GeminiLabs\SiteReviews\Compat\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Compat\Controllers\Controller;
use GeminiLabs\SiteReviews\Compat\Controllers\DeprecationController;
use GeminiLabs\SiteReviews\Compat\Controllers\HookController;
use GeminiLabs\SiteReviews\Database\OptionManager;
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
        $compat = glsr(OptionManager::class)->get('settings.advanced.compat', 'yes', 'bool');
        if ($compat) {
            $this->hook(HookController::class, [
                ['filterOptimizeAsset', 'wp_enqueue_scripts'],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v8', $compat)) {
            $this->hook(HookController::class, [
                ['filterReviewFormOrder', 'site-reviews/review-form/fields/order'],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v7', $compat)) {
            $this->hook(HookController::class, [
                ['filterReviewFormFieldsNormalized', 'site-reviews/review-form/fields/all', 10, 2],
                ['filterWhipWhitelist', 'site-reviews/whip/whitelist'],
            ]);
        }
        if (glsr()->filterBool('support/deprecated/v6', $compat)) {
            $this->hook(HookController::class, [
                ['filterRestApiReviewsProperties', 'site-reviews/rest-api/reviews/schema/properties', 9],
                ['filterRestApiSummaryProperties', 'site-reviews/rest-api/summary/schema/properties', 9],
                ['filterSlackCompose', 'site-reviews/slack/notification'],
                ['reviewSaved', 'site-reviews/review/updated', 10, 2],
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
