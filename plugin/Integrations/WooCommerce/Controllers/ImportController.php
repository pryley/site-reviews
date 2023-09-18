<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\CountProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Commands\ImportProductReviews;
use GeminiLabs\SiteReviews\Request;

class ImportController extends BaseController
{
    /**
     * @filter site-reviews/tools/general
     */
    public function filterTools(array $paths): array
    {
        $newPaths = [];
        foreach ($paths as $path) {
            if (Str::endsWith($path, 'import-reviews.php')) {
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
        if ('prepare' === $request->stage) { // @phpstan-ignore-line
            $result = $this->execute(new CountProductReviews($request));
            wp_send_json_success($result);
        }
        if ('import' === $request->stage) {
            $result = $this->execute(new ImportProductReviews($request));
            wp_send_json_success($result);
        }
    }
}
