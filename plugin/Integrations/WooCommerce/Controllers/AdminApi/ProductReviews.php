<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\AdminApi;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApi\ProductReviewsController;

class ProductReviews extends ProductReviewsController
{
    protected $namespace = 'wc-analytics';

    /**
     * @param \GeminiLabs\SiteReviews\Review $review
     *
     * @return array
     */
    protected function prepare_links($review)
    {
        $links = [
            'self' => [
                'href' => rest_url("/{$this->namespace}/{$this->rest_base}/{$review->ID}"),
            ],
            'collection' => [
                'href' => rest_url("/{$this->namespace}/{$this->rest_base}"),
            ],
        ];
        if (!empty($review->assigned_posts)) {
            $postId = Arr::get($review->assigned_posts, 0);
            $links['up'] = [
                'href' => rest_url("/{$this->namespace}/products/{$postId}"),
                'embeddable' => true,
            ];
        }
        if (0 !== $review->author_id) {
            $links['reviewer'] = [
                'href' => rest_url("wp/v2/users/{$review->user_id}"),
                'embeddable' => true,
            ];
        }
        return $links;
    }
}
