<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Commands\ImportReviewsCleanup;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\CountProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviewsAttachments;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviewsCleanup;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\MigrateProductRatings;
use GeminiLabs\SiteReviews\Request;

class ImportController extends AbstractController
{
    /**
     * @filter site-reviews/tools/general
     */
    public function filterTools(array $paths): array
    {
        $newPaths = [];
        foreach ($paths as $path) {
            if (str_ends_with($path, 'import-reviews.php')) {
                $newPaths[] = glsr()->path('views/integrations/woocommerce/tools/import-product-reviews.php');
            }
            $newPaths[] = $path;
        }
        return $newPaths;
    }

    /**
     * @action site-reviews/route/ajax/import-product-reviews
     */
    public function importProductReviewsAjax(Request $request): void
    {
        $stages = [
            1 => CountProductReviews::class,
            2 => ImportProductReviews::class,
            3 => ImportProductReviewsAttachments::class,
            4 => ImportProductReviewsCleanup::class,
        ];
        $stage = $request->cast('stage', 'int');
        if (array_key_exists($stage, $stages)) {
            $command = $this->execute(new $stages[$stage]($request));
            $command->sendJsonResponse();
        }
        wp_send_json_success();
    }

    /**
     * @action site-reviews/route/ajax/migrate-product-ratings
     */
    public function migrateProductRatingsAjax(Request $request): void
    {
        $command = $this->execute(new MigrateProductRatings($request));
        wp_send_json_success($command->response());
    }
}
