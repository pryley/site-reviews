<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApi;

class ReportReviewsTotalsController extends \WC_REST_Report_Reviews_Totals_Controller
{
    /**
     * @return array
     */
    protected function get_reports()
    {
        $ratings = glsr_get_ratings([
            'assigned_posts' => 'product',
        ]);
        $data = [];
        foreach ($ratings->ratings as $rating => $total) {
            if ($rating < 1) {
                continue;
            }
            $data[] = [
                'slug' => sprintf('rated_%s_out_of_5', $rating),
                'name' => sprintf(__('Rated %s out of 5', 'woocommerce'), $rating),
                'total' => $total,
            ];
        }
        return $data;
    }
}
