<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters\NormalizesArgs;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters\ReviewParameters;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Permissions\ReviewPermissions;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\PrepareReviewData;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\PrepareReviewLinks;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Response\PrepareReviewsResponse;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\ReviewSchema;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsShortcode;

class RestReviewController extends \WP_REST_Controller
{
    use NormalizesArgs;
    use ReviewPermissions;

    public function __construct()
    {
        $obj = get_post_type_object(glsr()->post_type);
        $namespace = $obj->rest_namespace ?? null;
        $this->namespace = is_string($namespace) && '' !== $namespace ? $namespace : glsr()->id.'/v1';
        $this->rest_base = 'reviews';
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function create_item($request)
    {
        $review = glsr_create_review($request->get_params());
        if (false === $review || !$review->isValid()) {
            $error = _x('Review creation failed, please check the Site Reviews console log for more details.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_review_create_item', $error, ['status' => 500]);
        }
        if ($request['_rendered']) {
            $response = $this->renderedItems($request);
        } else {
            $data = $this->prepare_item_for_response($review, $request);
            $response = rest_ensure_response($data);
        }
        $response->set_status(201);
        $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $review->ID)));
        return $response;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function delete_item($request)
    {
        $request->set_param('context', 'edit');
        $review = glsr_get_review($request['id']);
        if ((bool) $request['force']) {
            $previous = $this->prepare_item_for_response($review, $request);
            $result = wp_delete_post($review->ID, true);
            if (false === $result) {
                $error = _x('The review cannot be deleted.', 'admin-text', 'site-reviews');
                return new \WP_Error('rest_cannot_delete', $error, ['status' => 500]);
            }
            return rest_ensure_response([
                'deleted' => true,
                'previous' => $previous->get_data(),
            ]);
        }
        if (\EMPTY_TRASH_DAYS < 1) {
            /* translators: %s: force=true */
            $error = sprintf(_x('The review does not support trashing. Set "%s" to delete.', 'admin-text', 'site-reviews'), 'force=true');
            return new \WP_Error('rest_trash_not_supported', $error, ['status' => 501]);
        }
        if ('trash' === get_post_status($review->ID)) {
            $error = _x('The review has already been deleted.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_already_trashed', $error, ['status' => 410]);
        }
        if (!wp_trash_post($review->ID)) {
            $error = _x('The review cannot be deleted.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_delete', $error, ['status' => 500]);
        }
        return $this->prepare_item_for_response($review, $request);
    }

    /**
     * @return array
     */
    public function get_collection_params()
    {
        return glsr(ReviewParameters::class)->parameters(
            $this->get_context_param(['default' => 'view'])
        );
    }

    /**
     * @return array
     */
    public function get_endpoint_args_for_item_schema($method = \WP_REST_Server::CREATABLE)
    {
        $args = rest_get_endpoint_args_for_schema($this->get_item_schema(), $method);
        return glsr()->filterArray('rest-api/reviews/endpoint_args_for_schema', $args, $method);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_item($request)
    {
        if ($request['_rendered']) {
            return $this->renderedItem($request);
        }
        $review = glsr_get_review($request['id']);
        $data = $this->prepare_item_for_response($review, $request);
        return rest_ensure_response($data);
    }

    /**
     * @return array
     */
    public function get_item_schema()
    {
        if (empty($this->schema)) {
            $this->schema = glsr(ReviewSchema::class)->schema();
        }
        return $this->add_additional_fields_schema($this->schema);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_items($request)
    {
        if ($request['_rendered']) {
            return $this->renderedItems($request);
        }
        $results = glsr_get_reviews($this->normalizedArgs($request));
        $reviews = [];
        foreach ($results->reviews as $review) {
            if ($this->has_read_permission($review)) {
                $data = $this->prepare_item_for_response($review, $request);
                $reviews[] = $this->prepare_response_for_collection($data);
            }
        }
        if ($results->args['page'] > $results->max_num_pages && $results->total > 0) {
            $error = _x('The page number requested is larger than the number of pages available.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_invalid_page_number', $error, ['status' => 400]);
        }
        $response = rest_ensure_response($reviews);
        return $this->prepareResponse($response, $request, $results);
    }

    /**
     * @param Review           $review
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function prepare_item_for_response($review, $request)
    {
        $context = $request['context'] ?? 'view';
        $prepare = new PrepareReviewData(
            $this->get_fields_for_response($request),
            $review,
            $request
        );
        $data = $prepare->data();
        $data = $this->add_additional_fields_to_object($data, $request);
        $data = $this->filter_response_by_context($data, $context);
        $response = rest_ensure_response($data);
        $links = new PrepareReviewLinks($this->namespace, $this->rest_base);
        $response->add_links($links->links($review));
        if ('edit' === $context) {
            $response->add_links($links->editLinks($review));
        }
        return $response; // @todo filter this, i.e. "rest_prepare_{glsr()->post_type}"
    }

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, "/{$this->rest_base}", [
            'schema' => [$this, 'get_public_item_schema'],
            [
                'args' => $this->get_collection_params(),
                'callback' => [$this, 'get_items'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_items_permissions_check'],
            ],
            [
                'args' => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::CREATABLE),
                'callback' => [$this, 'create_item'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'create_item_permissions_check'],
            ],
        ]);
        register_rest_route($this->namespace, "/{$this->rest_base}".'/(?P<id>[\d]+)', [
            'args' => [
                'id' => [
                    'description' => _x('Unique identifier for the object.', 'admin-text', 'site-reviews'),
                    'type' => 'integer',
                ],
            ],
            'schema' => [$this, 'get_public_item_schema'],
            [
                'args' => [
                    'context' => $this->get_context_param(['default' => 'view']),
                ],
                'callback' => [$this, 'get_item'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_item_permissions_check'],
            ],
            [
                'args' => $this->get_endpoint_args_for_item_schema(\WP_REST_Server::EDITABLE),
                'callback' => [$this, 'update_item'],
                'methods' => \WP_REST_Server::EDITABLE,
                'permission_callback' => [$this, 'update_item_permissions_check'],
            ],
            [
                'args' => [
                    'force' => [
                        'default' => false,
                        'description' => _x('Whether to bypass Trash and force deletion.', 'admin-text', 'site-reviews'),
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'delete_item'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'delete_item_permissions_check'],
            ],
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function update_item($request)
    {
        $review = glsr_update_review($request['id'], $request->get_params());
        if (!$review) {
            $error = _x('Review update failed, please check the Site Reviews console log for more details.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_update_review', $error, ['status' => 500]);
        }
        $request->set_param('context', 'edit');
        if ($request['_rendered']) {
            return $this->renderedItem($request);
        }
        $data = $this->prepare_item_for_response($review, $request);
        $response = rest_ensure_response($data);
        return $response;
    }

    protected function normalizedArgs(\WP_REST_Request $request): array
    {
        return $this->normalizeArgs($request, $this->get_collection_params(), $this->rest_base);
    }

    protected function prepareResponse(\WP_REST_Response $response, \WP_REST_Request $request, Reviews $reviews): \WP_REST_Response
    {
        $prepare = new PrepareReviewsResponse($this->namespace, $this->rest_base);
        return $prepare->response($response, $request, $reviews, $this->normalizedArgs($request));
    }

    protected function renderedItem(\WP_REST_Request $request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        $args['hide'] = $request['_hide'] ?? $request['_rendered_hide'] ?? '';
        $review = glsr_get_review($request['id']);
        return rest_ensure_response([
            'rendered' => (string) $review->build($args),
        ]);
    }

    protected function renderedItems(\WP_REST_Request $request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        $args['hide'] = $request['_hide'] ?? $request['_rendered_hide'] ?? '';
        $html = glsr(SiteReviewsShortcode::class)
            ->normalize($args)
            ->buildReviewsHtml();
        $response = rest_ensure_response([
            'pagination' => $html->getPagination($wrap = false),
            'rendered' => $html->getReviews(),
        ]);
        return $this->prepareResponse($response, $request, $html->reviews);
    }
}
