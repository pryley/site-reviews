<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters\SummaryParameters;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\SummarySchema;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class RestSummaryController extends \WP_REST_Controller
{
    public function __construct()
    {
        $obj = get_post_type_object(glsr()->post_type);
        $this->namespace = !empty($obj->rest_namespace) ? $obj->rest_namespace : glsr()->id.'/v1';
        $this->rest_base = 'summary';
    }

    /**
     * @return array
     */
    public function get_collection_params()
    {
        return glsr(SummaryParameters::class)->parameters();
    }

    /**
     * @return array
     */
    public function get_item_schema()
    {
        if (empty($this->schema)) {
            $this->schema = glsr(SummarySchema::class)->schema();
        }
        return $this->add_additional_fields_schema($this->schema);
    }

    /**
     * @param \WP_REST_Request $request
     */
    public function get_rating_rendered($request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        $ratings = glsr_get_ratings($args);
        $rendered = glsr_star_rating(
            $ratings->average,
            $ratings->reviews,
            $args
        );
        return rest_ensure_response(
            array_merge(compact('args', 'rendered'), $ratings->toArray())
        );
    }

    /**
     * @param \WP_REST_Request $request
     */
    public function get_rating_summary($request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        if ($request['_rendered']) {
            $args['hide'] = $request['_hide'] ?? $request['_rendered_hide'] ?? '';
            return rest_ensure_response([
                'rendered' => glsr(SiteReviewsSummaryShortcode::class)->build($args, 'rest'),
            ]);
        }
        return rest_ensure_response(glsr_get_ratings($args)->toArray());
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function get_summary_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            $message = _x('Sorry, you are not allowed to view rating summaries.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, "/{$this->rest_base}/rating", [
            'schema' => [$this, 'get_public_item_schema'],
            [
                'args' => $this->get_collection_params(),
                'callback' => [$this, 'get_rating_rendered'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_summary_permissions_check'],
            ],
        ]);
        register_rest_route($this->namespace, "/{$this->rest_base}", [
            [
                'args' => $this->get_collection_params(),
                'callback' => [$this, 'get_rating_summary'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_summary_permissions_check'],
            ],
        ]);
    }

    protected function normalizedArgs(\WP_REST_Request $request): array
    {
        $args = [];
        $registered = $this->get_collection_params();
        foreach ($registered as $key => $params) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        if (empty($args['date'])) {
            $args['date'] = [
                'after' => $args['after'] ?? '',
                'before' => $args['before'] ?? '',
            ];
        }
        return glsr()->filterArray("rest-api/{$this->rest_base}/args", $args, $request);
    }
}
