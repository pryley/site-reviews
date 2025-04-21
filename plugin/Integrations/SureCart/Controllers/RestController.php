<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart\Controllers;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\HookProxy;
use SureCart\Models\Model;

class RestController implements ControllerContract
{
    use HookProxy;

    /**
     * @filter surecart/request/model
     */
    public function filterProductModel(Model $model, \WP_REST_Request $request): Model
    {
        if (!is_a($model, 'SureCart\Models\Product')) {
            return $model;
        }

        // glsr_log($model);
        // glsr_log($request->get_param('id'));
        // $modelId = $request['id'] ?? '';

        return $model;
    }


    /**
     * @param \WP_REST_Request|null $request The WP_REST_Request object
     * @param string|null          $method  The method of the request
     *
     * @return \WP_REST_Request|null
     *
     * @filter rest_products_request
     */
    public function filterProductsRequest($request = null, $method = null)
    {
        // if (!$request instanceof \WP_REST_Request || 'find' !== $method) { // methods can be create, edit, delete & find.
        if (!$request instanceof \WP_REST_Request) {
            return $request;
        }
        // Add your custom logic here.
        // For example, you can modify the metadata param in the checkout request.
        $request_metadata = $request->get_param('metadata');
        $metadata = (object) array_merge(
            $request_metadata,
            [
                'key1' => 'value1',
                'key2' => 'value2',
                'key3' => 'value3',
            ]
        );
        $request->set_param('metadata', $metadata);
        return $request;
    }

    /**
     * @filter site-reviews/rest-api/summary/args
     */
    public function filterRestApiSummaryArgs(array $args, \WP_REST_Request $request): array
    {
        if ('/site-reviews/v1/summary/stars' !== $request->get_route()) {
            return $args;
        }
        if (!str_contains((string) $request->get_param('_block'), 'surecart-product-rating')) {
            return $args;
        }
        $args['theme'] = glsr_get_option('integrations.surecart.style');
        return $args;
    }
}
