<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\CountProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviewsAttachments;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviewsCleanup;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\MigrateProductRatings;

class ImportController extends AbstractController
{
    /**
     * @filter site-reviews/import/stages
     */
    public function filterImportStages(array $stages, string $source): array
    {
        if ('woocommerce' === $source) {
            return [
                1 => CountProductReviews::class,
                2 => ImportProductReviews::class,
                3 => ImportProductReviewsAttachments::class,
                4 => ImportProductReviewsCleanup::class,
            ];
        }
        if ('woocommerce-ratings' === $source) {
            return [
                1 => MigrateProductRatings::class,
            ];
        }
        return $stages;
    }

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
}
