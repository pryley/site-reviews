<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Response;

use GeminiLabs\SiteReviews\Reviews;

class PrepareReviewsResponse
{
    public string $namespace;

    public string $restBase;

    public function __construct(string $namespace, string $restBase)
    {
        $this->namespace = $namespace;
        $this->restBase = $restBase;
    }

    public function response(\WP_REST_Response $response, \WP_REST_Request $request, Reviews $reviews, array $args): \WP_REST_Response
    {
        $page = $reviews->args['page'];
        $ratings = glsr_get_ratings($args);
        $response->header('X-GLSR-Average', (string) $ratings->average);
        $response->header('X-GLSR-Ranking', (string) $ratings->ranking);
        $response->header('X-WP-Total', (string) $reviews->total);
        $response->header('X-WP-TotalPages', (string) $reviews->max_num_pages);
        $parameters = $request->get_query_params();
        $base = add_query_arg(urlencode_deep($parameters), rest_url(sprintf('%s/%s', $this->namespace, $this->restBase)));
        if ($page > 1) {
            $prevPage = $page - 1;
            if ($prevPage > $reviews->max_num_pages) {
                $prevPage = $reviews->max_num_pages;
            }
            $prevLink = add_query_arg('page', $prevPage, $base);
            $response->link_header('prev', $prevLink);
        }
        if ($reviews->max_num_pages > $page) {
            $nextPage = $page + 1;
            $nextLink = add_query_arg('page', $nextPage, $base);
            $response->link_header('next', $nextLink);
        }
        return $response;
    }
}
