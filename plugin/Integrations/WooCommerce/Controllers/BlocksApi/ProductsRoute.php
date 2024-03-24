<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi;

use Automattic\WooCommerce\StoreApi\Routes\V1\Products as Route;
use Automattic\WooCommerce\StoreApi\Utilities\Pagination;

class ProductsRoute extends Route
{
    /**
     * @return \WP_REST_Response
     */
    protected function get_route_response(\WP_REST_Request $request)
    {
        $response = new \WP_REST_Response();
        $product_query = new ProductQuery();
        // Only get objects during GET requests.
        if (\WP_REST_Server::READABLE === $request->get_method()) {
            $query_results = $product_query->get_objects($request);
            $response_objects = [];
            foreach ($query_results['objects'] as $object) {
                $data = rest_ensure_response($this->schema->get_item_response($object)); // @phpstan-ignore-line
                $response_objects[] = $this->prepare_response_for_collection($data);
            }
            $response->set_data($response_objects);
        } else {
            $query_results = $product_query->get_results($request);
        }
        $response = (new Pagination())->add_headers($response, $request, $query_results['total'], $query_results['pages']);
        $response->header('Last-Modified', (string) $product_query->get_last_modified());
        return $response;
    }
}
