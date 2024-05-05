<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers;

use Automattic\WooCommerce\StoreApi\SchemaController;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Schemas\V1\ProductSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;
use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\HookProxy;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\AdminApi\ProductReviews;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi\ProductReviewSchema;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi\ProductReviewsRoute;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi\ProductsRoute;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApi\ProductReviewsController;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApi\ReportReviewsTotalsController;

class RestApiController implements ControllerContract
{
    use HookProxy;

    /**
     * @param array $endpoints
     *
     * @filter rest_endpoints
     */
    public function filterRestEndpoints($endpoints)
    {
        foreach ($endpoints as $route => &$endpoint) {
            if ('/wc-analytics/products/reviews' === $route) {
                $this->modifyAnalyticsReviewsEndpoint($endpoint);
            }
            if ('/wc-analytics/products/reviews/(?P<id>[\d]+)' === $route) {
                $this->modifyAnalyticsReviewEndpoint($endpoint);
            }
            if ('/wc-analytics/products/reviews/batch' === $route) {
                $this->modifyAnalyticsBatchEndpoint($endpoint);
            }
            if (in_array($route, ['/wc/store/v1/products', '/wc/store/v1/products/reviews'])) {
                $this->modifyStoreEndpoints($endpoint);
            }
        }
        return $endpoints;
    }

    /**
     * @param array $namespaces
     *
     * @filter woocommerce_rest_api_get_rest_namespaces
     */
    public function filterRestNamespaces($namespaces)
    {
        $namespaces['wc/v3']['product-reviews'] = ProductReviewsController::class;
        $namespaces['wc/v3']['reports-reviews-totals'] = ReportReviewsTotalsController::class;
        return $namespaces;
    }

    /**
     * @param bool   $hasPermission
     * @param string $context
     * @param int    $objectId
     * @param string $permissionType
     *
     * @return bool
     *
     * @filter woocommerce_rest_check_permissions
     */
    public function filterRestPermissions($hasPermission, $context, $objectId, $permissionType)
    {
        if ('product_review' === $permissionType) {
            $contexts = [
                'read' => 'edit_posts',
                'create' => 'create_posts',
                'edit' => 'edit_posts',
                'delete' => 'delete_posts',
                'batch' => '', // disabled
            ];
            if (isset($contexts[$context])) {
                return glsr()->can($contexts[$context]);
            }
        }
        return $hasPermission;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Database\Query $query
     *
     * @filter site-reviews/query/sql/join
     */
    public function filterSqlJoin(array $join, string $handle, $query): array
    {
        if ('woocommerce' !== ($query->args['integration'] ?? '')) {
            return $join;
        }
        $orderby = Arr::get($query->args, 'orderby');
        if (str_ends_with($orderby, 'rating')) {
            $join['woo_orderby_rating'] = "INNER JOIN {$query->db->posts} AS p ON (p.ID = r.review_id)";
        }
        return $join;
    }

    /**
     * @param \GeminiLabs\SiteReviews\Database\Query $query
     *
     * @filter site-reviews/query/sql/order-by
     */
    public function filterSqlOrderBy(array $orderBy, string $handle, $query): array
    {
        if ('woocommerce' !== ($query->args['integration'] ?? '')) {
            return $orderBy;
        }
        $order = Arr::get($query->args, 'order');
        $orderby = Arr::get($query->args, 'orderby');
        if (str_ends_with($orderby, 'rating')) {
            return [
                "r.rating {$order}",
                "p.post_date {$order}",
            ];
        }
        if (str_ends_with($orderby, 'date')) {
            return [
                "p.post_date {$order}", // ignore pinned reviews
            ];
        }
        if (str_ends_with($orderby, 'date_gmt')) {
            return [
                "p.post_date_gmt {$order}", // ignore pinned reviews
            ];
        }
        return $orderBy;
    }

    /**
     * @param array $endpoint
     *
     * @return void
     */
    protected function modifyAnalyticsBatchEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_batch_schema'];
                continue;
            }
            if ('POST, PUT, PATCH' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'batch_items'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'batch_items_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     *
     * @return void
     */
    protected function modifyAnalyticsReviewEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_item_schema'];
                continue;
            }
            if ('DELETE' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'delete_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'delete_item_permissions_check'];
            }
            if ('GET' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'get_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'get_item_permissions_check'];
            }
            if ('POST, PUT, PATCH' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'update_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'update_item_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     *
     * @return void
     */
    protected function modifyAnalyticsReviewsEndpoint(&$endpoint)
    {
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key) {
                $endpoint[$key] = [glsr(ProductReviews::class), 'get_public_item_schema'];
                continue;
            }
            if ('GET' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'get_items'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'get_items_permissions_check'];
            }
            if ('POST' === Arr::get($value, 'methods')) {
                $endpoint[$key]['callback'] = [glsr(ProductReviews::class), 'create_item'];
                $endpoint[$key]['permission_callback'] = [glsr(ProductReviews::class), 'create_item_permissions_check'];
            }
        }
    }

    /**
     * @param array $endpoint
     *
     * @return void
     */
    protected function modifyStoreEndpoints(&$endpoint)
    {
        $controller = StoreApi::container()->get(SchemaController::class);
        $extend = StoreApi::container()->get(ExtendSchema::class);
        $productSchema = new ProductSchema($extend, $controller);
        $productRoutes = new ProductsRoute($controller, $productSchema);
        $reviewSchema = new ProductReviewSchema($extend, $controller);
        $reviewRoutes = new ProductReviewsRoute($controller, $reviewSchema);
        foreach ($endpoint as $key => $value) {
            if ('schema' === $key && isset($value[0])) {
                if (is_a($value[0], 'Automattic\WooCommerce\StoreApi\Schemas\V1\ProductReviewSchema')) {
                    $endpoint[$key] = [$reviewSchema, 'get_public_item_schema'];
                }
                continue;
            }
            if (!isset($value['callback'])) {
                continue;
            }
            if (is_a($value['callback'][0], 'Automattic\WooCommerce\StoreApi\Routes\V1\Products')) {
                $endpoint[$key]['callback'] = [$productRoutes, 'get_response'];
            }
            if (is_a($value['callback'][0], 'Automattic\WooCommerce\StoreApi\Routes\V1\ProductReviews')) {
                $endpoint[$key]['callback'] = [$reviewRoutes, 'get_response'];
            }
        }
    }
}
