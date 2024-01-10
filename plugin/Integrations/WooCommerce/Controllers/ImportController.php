<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\CountProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\RepairProductRatings;
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
            if (str_ends_with($path, 'repair-review-relations.php')) {
                $newPaths[] = glsr()->path('views/integrations/woocommerce/tools/repair-product-ratings.php');
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
        if ('prepare' === $request->stage) { // @phpstan-ignore-line
            $command = $this->execute(new CountProductReviews($request));
            wp_send_json_success($command->response());
        }
        if ('import' === $request->stage) {
            $command = $this->execute(new ImportProductReviews($request));
            wp_send_json_success($command->response());
        }
    }

    /**
     * @action site-reviews/route/ajax/repair-product-ratings
     */
    public function repairProductRatingsAjax(): void
    {
        $command = $this->execute(new RepairProductRatings());
        wp_send_json_success($command->response());
    }
}
