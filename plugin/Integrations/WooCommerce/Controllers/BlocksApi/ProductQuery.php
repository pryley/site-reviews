<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\BlocksApi;

use Automattic\WooCommerce\StoreApi\Utilities\ProductQuery as Query;

class ProductQuery extends Query
{
    /**
     * Prepare query args to pass to WP_Query for a REST API request.
     *
     * @param \WP_REST_Request $request request data
     *
     * @return array
     */
    public function prepare_objects_query($request)
    {
        $args = parent::prepare_objects_query($request);
        if ('rating' !== $args['orderby']) {
            return $args;
        }
        if (empty($args['meta_query'])) {
            $args['meta_query'] = [];
        }
        if ('bayesian' === glsr_get_option('integrations.woocommerce.sorting')) {
            $args['meta_query'][] = $this->buildMetaQuery('glsr_ranking', '_glsr_ranking');
            $args['orderby'] = ['glsr_ranking' => 'DESC'];
        } else {
            $args['meta_query'][] = $this->buildMetaQuery('glsr_average', '_glsr_average');
            $args['meta_query'][] = $this->buildMetaQuery('glsr_reviews', '_glsr_reviews');
            $args['orderby'] = ['glsr_average' => 'DESC', 'glsr_reviews' => 'DESC'];
        }
        return $args;
    }

    /**
     * @param string $orderbyKey
     * @param string $metaKey
     *
     * @return array
     */
    protected function buildMetaQuery($orderbyKey, $metaKey)
    {
        return [
            'relation' => 'OR',
            $orderbyKey => ['key' => $metaKey, 'compare' => 'NOT EXISTS'], // this comes first!
            ['key' => $metaKey, 'compare' => 'EXISTS'],
        ];
    }
}
