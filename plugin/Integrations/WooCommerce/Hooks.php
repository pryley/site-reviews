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
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/enabled', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/style', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/summary', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/reviews', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/form', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/sorting', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/display_empty', 10, 3],
            ['filterOrphanedOptions', 'site-reviews/option/addon/woocommerce/wp_comments', 10, 3],
            ['filterSettings', 'site-reviews/settings'],
            ['filterSettingsCallback', 'site-reviews/settings/sanitize', 10, 2],
            ['filterSubsubsub', 'site-reviews/integration/subsubsub'],
            ['renderNotice', 'admin_init'],
            ['renderSettings', 'site-reviews/settings/woocommerce'],
        ]);
        $this->hook(ImportController::class, [
            ['filterTools', 'site-reviews/tools/general'],
            ['importProductReviewsAjax', 'site-reviews/route/ajax/import-product-reviews'],
            ['migrateProductRatingsAjax', 'site-reviews/route/ajax/migrate-product-ratings'],
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
        if ('yes' !== $this->option('integrations.woocommerce.wp_comments')) {
            return [];
        }
        return [
            ['filterProductCommentMeta', 'get_comment_metadata', 20, 4],
            ['filterProductCommentsQuery', 'comments_pre_query', 20, 2],
        ];
    }

    protected function isEnabled(): bool
    {
        return 'yes' === $this->option('integrations.woocommerce.enabled')
            && 'yes' === get_option('woocommerce_enable_reviews', 'yes')
            && class_exists('WooCommerce')
            && function_exists('WC');
    }

    protected function isWooBlockTheme(): bool
    {
        if (!class_exists('Automattic\WooCommerce\Blocks\Utils\BlockTemplateUtils')) {
            return false;
        }
        return \Automattic\WooCommerce\Blocks\Utils\BlockTemplateUtils::supports_block_templates();
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
        $hooks = [
            ['filterCommentsTemplate', 'comments_template', 50],
            ['filterGetRatingHtml', 'woocommerce_product_get_rating_html', 20, 3],
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
            ['registerMetaboxes', 'add_meta_boxes_product', 20],
            ['renderBulkEditField', 'bulk_edit_custom_box', 10, 2],
            ['renderLoopRating', 'site-reviews/woocommerce/render/loop/rating', 5],
            ['renderProductDataPanel', 'woocommerce_product_data_panels'],
            ['renderQuickEditField', 'quick_edit_custom_box', 5, 2],
            ['renderReviews', 'site-reviews/woocommerce/render/product/reviews'],
            ['updateProductData', 'woocommerce_admin_process_product_object'],
            ['updateProductRatingCounts', 'site-reviews/ratings/count/post', 10, 2],
        ];
        if (!$this->isWooBlockTheme()) {
            $hooks[] = ['renderTitleRating', 'woocommerce_single_product_summary'];
        }
        return $hooks;
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
