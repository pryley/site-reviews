<?php

namespace GeminiLabs\SiteReviews\Integrations\WooCommerce\Controllers\RestApi;

use Automattic\WooCommerce\StoreApi\Utilities\Pagination;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class ProductReviewsController extends \WC_REST_Product_Reviews_Controller
{
    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function batch_items_permissions_check($request)
    {
        return $this->checkPermissions('batch', $request);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function create_item($request)
    {
        if ('product' !== get_post_type($request['product_id'])) {
            return new \WP_Error('woocommerce_rest_product_invalid_id', __('Invalid product ID.', 'woocommerce'), ['status' => 404]);
        }
        $args = $this->prepare_item_for_database($request);
        if (empty($args['content'])) {
            return new \WP_Error('woocommerce_rest_review_content_invalid', __('Invalid review content.', 'woocommerce'), ['status' => 400]);
        }
        if ($review = glsr_create_review($args)) {
            glsr()->action('woocommerce/rest-api/insert_product_review', $review, $request, true);
            $updateAdditionalFields = $this->update_additional_fields_for_object($review, $request);
            if (is_wp_error($updateAdditionalFields)) {
                return $updateAdditionalFields;
            }
            $context = glsr()->can('edit_posts') ? 'edit' : 'view';
            $request->set_param('context', $context);
            $response = $this->prepare_item_for_response($review, $request);
            $response = rest_ensure_response($response);
            $response->set_status(201);
            $response->header('Location', rest_url(sprintf('%s/%s/%d', $this->namespace, $this->rest_base, $review->ID)));
            return $response;
        }
        return new \WP_Error('woocommerce_rest_review_failed_create', __('Creating product review failed.', 'woocommerce'), ['status' => 500]);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function create_item_permissions_check($request)
    {
        return $this->checkPermissions('create', $request);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function delete_item($request)
    {
        $review = $this->get_review($request['id']);
        if (is_wp_error($review)) {
            return $review;
        }
        $force = Arr::getAs('bool', $request, 'force', false);
        $supportsTrash = apply_filters('woocommerce_rest_product_review_trashable', EMPTY_TRASH_DAYS > 0, $review);
        $request->set_param('context', 'edit');
        if ($force) {
            $previous = $this->prepare_item_for_response($review, $request);
            $result = wp_delete_post($review->ID, $force);
            $response = new \WP_REST_Response();
            $response->set_data([
                'deleted' => true,
                'previous' => $previous->get_data(),
            ]);
        } else {
            if (!$supportsTrash) {
                return new \WP_Error('woocommerce_rest_trash_not_supported', sprintf(__("The object does not support trashing. Set '%s' to delete.", 'woocommerce'), 'force=true'), ['status' => 501]);
            }
            if ('trash' === $review->status) {
                return new \WP_Error('woocommerce_rest_already_trashed', __('The object has already been trashed.', 'woocommerce'), ['status' => 410]);
            }
            $result = wp_trash_post($review->ID);
            $review->refresh(); // refresh the review!
            $response = $this->prepare_item_for_response($review, $request);
        }
        if (!$result) {
            return new \WP_Error('woocommerce_rest_cannot_delete', __('The object cannot be deleted.', 'woocommerce'), ['status' => 500]);
        }
        glsr()->action('woocommerce/rest-api/delete_review', $review, $response, $request);
        return $response;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function delete_item_permissions_check($request)
    {
        return $this->checkPermissions('delete', $request);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function get_item_permissions_check($request)
    {
        return $this->checkPermissions('read', $request);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_items($request)
    {
        $registered = $this->get_collection_params(); // @todo support (hold, spam, trash) post_status
        $mappedKeys = [
            'after' => 'date_after', // string
            'before' => 'date_before', // string
            'exclude' => 'post__not_in', // int[]
            'include' => 'post__in', // int[]
            'product' => 'assigned_posts', // int[]
            'reviewer' => 'user__in', // int[]
            'reviewer_email' => 'email', // string
            'reviewer_exclude' => 'user__not_in', // int[]
        ];
        $args = [];
        foreach ($registered as $key => $params) {
            if (isset($request[$key])) {
                $mappedKey = Arr::get($mappedKeys, $key, $key);
                $args[$mappedKey] = $request[$key];
            }
        }
        if (empty($args['assigned_posts'])) {
            // @todo use the post_type once Site Reviews supports it!
            $args['assigned_posts'] = 'product';
        }
        $results = glsr_get_reviews($args); // @todo only return product reviews!
        $reviews = [];
        foreach ($results->reviews as $review) {
            if ($this->checkPermissionForProductReview('read', $review->ID)) {
                $data = $this->prepare_item_for_response($review, $request);
                $reviews[] = $this->prepare_response_for_collection($data);
            }
        }
        $response = rest_ensure_response($reviews);
        $response = (new Pagination())->add_headers($response, $request, $results->total, $results->max_num_pages);
        return $response;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function get_items_permissions_check($request)
    {
        return $this->checkPermissions('list', $request);
    }

    /**
     * @param Review           $review
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response $response
     */
    public function prepare_item_for_response($review, $request) // @phpstan-ignore-line
    {
        $context = Arr::get($request, 'context', 'view');
        $fields = $this->get_fields_for_response($request);
        $data = [
            'id' => $review->ID,
            'date_created' => wc_rest_prepare_date_response($review->date),
            'date_created_gmt' => wc_rest_prepare_date_response($review->date_gmt),
            'product_id' => Arr::get($review->assigned_posts, 0),
            'status' => $this->prepare_status_response($review->status),
            'reviewer' => $review->author,
            'reviewer_email' => $review->email,
            'review' => 'view' === $context ? wpautop($review->content) : $review->content,
            'rating' => $review->rating,
            'verified' => $review->hasVerifiedOwner(), // @phpstan-ignore-line
            'reviewer_avatar_urls' => rest_get_avatar_urls($review->email),
        ];
        foreach ($data as $key => $value) {
            if (!in_array($key, $fields, true)) {
                unset($data[$key]);
            }
        }
        $data = $this->add_additional_fields_to_object($data, $request);
        $data = $this->filter_response_by_context($data, $context);
        $response = rest_ensure_response($data);
        $response->add_links($this->prepare_links($review));
        return glsr()->filter('woocommerce/rest-api/prepare_product_review', $response, $review, $request);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function update_item($request)
    {
        $review = $this->get_review($request['id']);
        if (is_wp_error($review)) {
            return $review;
        }
        // update review post
        $postArgs = $this->prepare_item_for_update($request);
        if (is_wp_error($postArgs)) {
            return $postArgs;
        }
        $postArgs['ID'] = $review->ID;
        $updatePost = wp_update_post($postArgs, $wperror = true);
        if (is_wp_error($updatePost)) {
            return new \WP_Error('woocommerce_rest_comment_failed_edit', __('Updating review failed.', 'woocommerce'), ['status' => 500]);
        }
        // update rating entry
        glsr(ReviewManager::class)->update($review->ID, $this->prepare_item_for_database($request));
        $review->refresh(); // refresh the review!
        glsr()->action('woocommerce/rest-api/insert_product_review', $review, $request, false);
        $updateAdditionalFields = $this->update_additional_fields_for_object($review, $request);
        if (is_wp_error($updateAdditionalFields)) {
            return $updateAdditionalFields;
        }
        $request->set_param('context', 'edit');
        $response = $this->prepare_item_for_response($review, $request);
        return rest_ensure_response($response);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function update_item_permissions_check($request)
    {
        return $this->checkPermissions('edit', $request);
    }

    /**
     * @param string $context
     * @param int    $reviewId
     *
     * @return bool
     */
    protected function checkPermissionForProductReview($context = 'read', $reviewId = 0)
    {
        $contexts = [
            'batch' => 'edit_post',
            'create' => 'create_post',
            'delete' => 'delete_post',
            'edit' => 'edit_post',
            'read' => 'edit_post',
            'list' => 'edit_post',
        ];
        if (!isset($contexts[$context])) {
            return false;
        }
        if ($reviewId > 0) {
            if (!glsr_get_review($reviewId)->isValid()) {
                return false;
            }
            return glsr()->can($contexts[$context], $reviewId);
        }
        return glsr()->can($contexts[$context].'s');
    }

    /**
     * @param string           $action
     * @param \WP_REST_Request $request
     *
     * @return bool|\WP_Error
     */
    protected function checkPermissions($action, $request)
    {
        $errors = [
            'batch' => ['woocommerce_rest_cannot_batch', __('Sorry, you are not allowed to batch manipulate this resource.', 'woocommerce')],
            'create' => ['woocommerce_rest_cannot_create', __('Sorry, you are not allowed to create resources.', 'woocommerce')],
            'delete' => ['woocommerce_rest_cannot_delete', __('Sorry, you cannot delete this resource.', 'woocommerce')],
            'edit' => ['woocommerce_rest_cannot_edit', __('Sorry, you cannot edit this resource.', 'woocommerce')],
            'list' => ['woocommerce_rest_cannot_view', __('Sorry, you cannot list resources.', 'woocommerce')],
            'read' => ['woocommerce_rest_cannot_view', __('Sorry, you cannot view this resource.', 'woocommerce')],
        ];
        if (in_array($action, ['delete', 'edit', 'read'])) {
            $hasPermission = $this->checkPermissionForProductReview($action, $request['id']);
        } else {
            $hasPermission = $this->checkPermissionForProductReview($action);
        }
        if ($hasPermission) {
            return true;
        }
        if (array_key_exists($action, $errors)) {
            return new \WP_Error($errors[$action][0], $errors[$action][1], [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return false;
    }

    /**
     * @param int $id
     *
     * @return Review|\WP_Error
     */
    protected function get_review($id)
    {
        $review = glsr_get_review($id);
        $error = new \WP_Error('woocommerce_rest_review_invalid_id', __('Invalid review ID.', 'woocommerce'), ['status' => 404]);
        if (!$review->isValid()) {
            return $error;
        }
        if (!empty($review->assigned_posts)) {
            if ('product' !== get_post_type((int) Arr::get($review->assigned_posts, 0))) {
                return new \WP_Error('woocommerce_rest_product_invalid_id', __('Invalid product ID.', 'woocommerce'), ['status' => 404]);
            }
        }
        return $review;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return array
     */
    protected function prepare_item_for_database($request)
    {
        $mappedKeys = [
            'date_created' => 'date',
            'product_id' => 'assigned_posts',
            'rating' => 'rating',
            'review' => 'content',
            'reviewer' => 'name',
            'reviewer_email' => 'email',
        ];
        $args = [];
        foreach ($mappedKeys as $requestKey => $key) {
            if (isset($request[$requestKey])) {
                $args[$key] = $request[$requestKey];
            }
        }
        return $args;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return array|\WP_Error
     */
    protected function prepare_item_for_update($request)
    {
        $mappedKeys = [
            'content' => 'post_content',
            'status' => 'post_status',
            'title' => 'post_title',
        ];
        $status = [
            '0' => 'pending',
            '1' => 'publish',
            'approve' => 'publish',
            'approved' => 'publish',
            'draft' => 'draft',
            'hold' => 'pending',
            'pending' => 'pending',
            'publish' => 'publish',
            'unapprove' => 'pending',
            'unapproved' => 'pending',
        ];
        $args = [];
        foreach ($mappedKeys as $requestKey => $key) {
            if ('status' === $requestKey) {
                $args[$key] = Arr::get($status, $request[$requestKey]);
                continue;
            }
            if (isset($request[$requestKey])) {
                $args[$key] = $request[$requestKey];
            }
        }
        return array_filter($args);
    }

    /**
     * @param Review $review
     *
     * @return array
     */
    protected function prepare_links($review) // @phpstan-ignore-line
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
            ];
        }
        if (0 !== $review->author_id) {
            $links['reviewer'] = [
                'href' => rest_url("wp/v2/users/{$review->author_id}"),
                'embeddable' => true,
            ];
        }
        return $links;
    }

    /**
     * @param string|int $status
     *
     * @return string
     */
    protected function prepare_status_response($status)
    {
        if (in_array($status, ['0', 'hold', 'pending', 'unapprove', 'unapproved'])) {
            return 'hold';
        }
        if (in_array($status, ['1', 'approve', 'approved', 'publish'])) {
            return 'approved';
        }
        return $status;
    }
}
