<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Integrations\SureCart\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\SureCart\Controllers\ProductController;
use GeminiLabs\SiteReviews\Integrations\SureCart\Controllers\RestController;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
            $this->hook(RestController::class, [
                ['filterRestApiSummaryArgs', 'site-reviews/rest-api/summary/args', 10, 2],
            ]);
        $this->hook(Controller::class, [
            ['filterSettings', 'site-reviews/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['filterSubsubsub', 'site-reviews/integration/subsubsub'],
            ['renderNotice', 'admin_init'],
            ['renderSettings', 'site-reviews/settings/surecart'],
        ]);
        if ($this->isEnabled()) {
            $this->hook(ProductController::class, [
                ['filterAssignedPostsPostId', 'render_block_core/shortcode', 10, 3],
                ['filterBlockRenderCallback', 'block_type_metadata_settings', 15, 2],
                ['filterReviewAuthorTagValue', 'site-reviews/review/value/author', 10, 2],
                ['filterReviewCallbackHasProductOwner', 'site-reviews/review/call/hasProductOwner'],
                ['filterReviewFormBuild', 'site-reviews/build/template/reviews-form', 10, 2],
                ['filterShortcodeAttributes', 'site-reviews/shortcode/site_reviews/attributes', 10, 2],
                ['filterShortcodeAttributes', 'site-reviews/shortcode/site_reviews_form/attributes', 10, 2],
                ['filterShortcodeAttributes', 'site-reviews/shortcode/site_reviews_summary/attributes', 10, 2],
                ['parseProductQuery', 'parse_query'],
                // ['registerBlockPatterns', 'init'],
                ['registerBlocks', 'init', 11],
                ['registerProductAttributes', 'surecart/product/attributes_set'],
                ['verifyProductOwner', 'site-reviews/review/created', 20],
            ]);
            $this->hook(RestController::class, [
                ['filterProductModel', 'surecart/request/model', 10, 2],
                ['filterProductsRequest', 'rest_products_request'],
                ['filterRestApiSummaryArgs', 'site-reviews/rest-api/summary/args', 10, 2],
            ]);
        }
    }

    protected function isEnabled(): bool
    {
        return $this->isInstalled()
            && 'yes' === $this->option('integrations.surecart.enabled');
    }

    protected function isInstalled(): bool
    {
        return class_exists('SureCart');
    }
}
