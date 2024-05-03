<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi;

use Automattic\WooCommerce\StoreApi\Routes\V1\ProductReviews as Route;
use Automattic\WooCommerce\StoreApi\Utilities\Pagination;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ProductReviewsRoute extends Route
{
    /**
     * @return \WP_REST_Response
     */
    protected function get_route_response(\WP_REST_Request $request)
    {
        $args = [
            'assigned_posts' => Arr::uniqueInt($request['product_id']),
            'offset' => $request['offset'],
            'order' => $request['order'], // asc|desc
            'orderby' => $request['orderby'], // rating|date|date_gmt
            'per_page' => $request['per_page'],
        ];
        if ($categoryIds = Arr::uniqueInt($request['category_id'])) {
            $childIds = [];
            foreach ($categoryIds as $categoryId) {
                $termChildIds = get_term_children($categoryId, 'product_cat');
                if (!is_wp_error($termChildIds)) {
                    $childIds = array_merge($childIds, $termChildIds);
                }
            }
            $categoryIds = array_unique(array_merge($categoryIds, $childIds));
            $productIds = get_objects_in_term($categoryIds, 'product_cat');
            $args['assigned_posts'] = array_merge($args['assigned_posts'], $productIds);
        }
        if (empty($args['assigned_posts'])) {
            $args['assigned_posts'] = 'product';
        }
        $args['integration'] = 'woocommerce';
        $results = glsr_get_reviews($args);
        $reviews = [];
        foreach ($results->reviews as $review) {
            $data = $this->prepare_item_for_response($review, $request);
            $reviews[] = $this->prepare_response_for_collection($data);
        }
        $response = rest_ensure_response($reviews);
        $response = (new Pagination())->add_headers($response, $request, $results->total, $results->max_num_pages);
        return $response;
    }

    /**
     * @param string $param
     *
     * @return string
     */
    protected function normalize_query_param($param)
    {
        return $param;
    }
}
