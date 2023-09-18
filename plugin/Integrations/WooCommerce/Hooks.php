<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\Controller;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ExperimentsController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ImportController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\MainController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\ProductController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApiController;

class Hooks extends AbstractHooks
{
    public function run(): void
    {
        $this->hook(Controller::class, [
            ['declareHposCompatibility', 'before_woocommerce_init'],
            ['filterSettings', 'site-reviews/addon/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['filterSubsubsub', 'site-reviews/addon/subsubsub'],
            ['renderNotice', 'admin_init'],
            ['renderSettings', 'site-reviews/addon/settings/woocommerce'],
        ]);
        $this->hook(ImportController::class, [
            ['filterTools', 'site-reviews/tools/general'],
            ['importProductReviewsAjax', 'site-reviews/route/ajax/import-product-reviews'],
        ]);
        if ($this->isEnabled()) {
            $this->hook(ExperimentsController::class, $this->experimentalHooks());
            $this->hook(MainController::class, $this->mainHooks());
            $this->hook(ProductController::class, $this->productHooks());
            $this->hook(RestApiController::class, $this->restApiHooks());
        }
    }

    protected function experimentalHooks(): array
    {
        if ('yes' !== glsr_get_option('addons.woocommerce.wp_comments')) {
            return [];
        }
        return [
            ['filterProductCommentMeta', 'get_comment_metadata', 20, 4],
            ['filterProductCommentsQuery', 'comments_pre_query', 20, 2],
        ];
    }

    protected function isEnabled(): bool
    {
        return glsr_get_option('addons.woocommerce.enabled', false, 'bool')
            && 'yes' === get_option('woocommerce_enable_reviews', 'yes')
            && class_exists('WooCommerce')
            && function_exists('WC');
    }

    protected function mainHooks(): array
    {
        remove_action('comment_post', ['WC_Comments', 'add_comment_purchase_verification'], 10);
        remove_action('wp_update_comment_count', ['WC_Comments', 'clear_transients'], 10);
        remove_filter('comments_open', ['WC_Comments', 'comments_open'], 10);
        return [
            ['filterInlineStyles', 'site-reviews/enqueue/public/inline-styles', 20],
            ['filterProductCommentStatus', 'get_default_comment_status', 10, 3],
            ['filterProductSettings', 'woocommerce_get_settings_products', 10, 2],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
            ['filterRatingOption', 'option_woocommerce_enable_review_rating'],
            ['filterRatingOption', 'option_woocommerce_review_rating_required'],
            ['filterReviewAuthorTagValue', 'site-reviews/review/value/author', 10, 2],
            ['filterReviewProductMethod', 'site-reviews/review/call/product'],
            ['filterStarImages', 'site-reviews/config/inline-styles', 20],
            ['hasVerifiedOwner', 'site-reviews/review/call/hasVerifiedOwner'],
            ['registerElementorWidgets', 'elementor/widgets/register', 20],
            ['registerWidgets', 'widgets_init', 20],
            ['removeWoocommerceReviews', 'woocommerce_register_post_type_product'],
            ['renderNotice', 'admin_notices'],
            ['verifyProductOwner', 'site-reviews/review/created', 20],
        ];
    }

    protected function productHooks(): array
    {
        return [
            ['filterCommentsTemplate', 'comments_template', 50],
            ['filterGetRatingHtml', 'woocommerce_product_get_rating_html', 10, 3],
            ['filterGetStarRatingHtml', 'woocommerce_get_star_rating_html', 10, 3],
            ['filterProductAverageRating', 'woocommerce_product_get_average_rating', 10, 2],
            ['filterProductDataTabs', 'woocommerce_product_data_tabs'],
            ['filterProductMetaQuery', 'woocommerce_product_query_meta_query', 20],
            ['filterProductPostClauses', 'woocommerce_get_catalog_ordering_args', 20, 2],
            ['filterProductRatingCounts', 'woocommerce_product_get_rating_counts', 10, 2],
            ['filterProductReviewCount', 'woocommerce_product_get_review_count', 10, 2],
            ['filterProductTabs', 'woocommerce_product_tabs'],
            ['filterProductTaxQuery', 'woocommerce_product_query_tax_query', 20],
            ['filterStructuredData', 'woocommerce_structured_data_product', 10, 2],
            ['filterWidgetArgsTopRatedProducts', 'woocommerce_top_rated_products_widget_args'],
            ['filterWoocommerceTemplate', 'wc_get_template', 20, 2],
            ['modifyProductQuery', 'pre_get_posts'],
            ['printInlineStyle', 'admin_head'],
            ['renderLoopRating', 'site-reviews/woocommerce/render/loop/rating', 5],
            ['renderProductDataPanel', 'woocommerce_product_data_panels'],
            ['renderReviews', 'site-reviews/woocommerce/render/product/reviews'],
            ['renderTitleRating', 'woocommerce_single_product_summary'],
            ['updateProductData', 'woocommerce_admin_process_product_object'],
        ];
    }

    protected function restApiHooks(): array
    {
        return [
            ['filterRestEndpoints', 'rest_endpoints'],
            ['filterRestNamespaces', 'woocommerce_rest_api_get_rest_namespaces'],
            ['filterRestPermissions', 'woocommerce_rest_check_permissions', 10, 4],
            ['filterSqlJoin', 'site-reviews/query/sql/join', 10, 3],
            ['filterSqlOrderBy', 'site-reviews/query/sql/order-by', 10, 3],
        ];
    }
}
