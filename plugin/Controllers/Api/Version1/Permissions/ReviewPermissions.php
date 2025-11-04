<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Permissions;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

trait ReviewPermissions
{
    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function create_item_permissions_check($request)
    {
        if (!empty($request['id'])) {
            $error = _x('Cannot create existing review.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_review_exists', $error, ['status' => 400]);
        }
        if (!glsr()->can('create_posts')) {
            $error = _x('Sorry, you are not allowed to create reviews as this user.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_create', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!$this->has_edit_others_permission($request)) {
            $error = _x('Sorry, you are not allowed to create reviews as this review author.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_edit_others', $error, ['status' => rest_authorization_required_code()]);
        }
        if (!$this->has_assign_terms_permission($request)) {
            $error = _x('Sorry, you are not allowed to assign the provided terms.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_assign_term', $error, ['status' => rest_authorization_required_code()]);
        }
        return true;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function delete_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $message = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_review_invalid_id', $message, [
                'status' => 404,
            ]);
        }
        if (!glsr()->can('delete_post', $review->ID)) {
            $message = _x('Sorry, you are not allowed to delete this review.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_delete', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function get_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $message = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_review_invalid_id', $message, [
                'status' => 404
            ]);
        }
        if (!$this->has_read_permission($review)) {
            $message = _x('Sorry, you are not allowed to view this review.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_view', $message, [
                'status' => rest_authorization_required_code()
            ]);
        }
        return true;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function get_items_permissions_check($request)
    {
        if (!is_user_logged_in()) {
            $message = _x('Sorry, you do not have permission to access reviews.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        $context = $request['context'] ?? 'edit';
        if ('edit' === $context && !glsr()->can('edit_posts')) {
            $message = _x('Sorry, you are not allowed to edit reviews.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function update_item_permissions_check($request)
    {
        $review = glsr_get_review($request['id']);
        if (!$review->isValid()) {
            $message = _x('Invalid review ID.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_review_invalid_id', $message, [
                'status' => 404,
            ]);
        }
        if (!glsr()->can('edit_post', $review->ID)) {
            $message = _x('Sorry, you are not allowed to edit this review.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_edit', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        if (!$this->has_edit_others_permission($request)) {
            $message = _x('Sorry, you are not allowed to update the review as this user.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_edit_others', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        if (!$this->has_assign_terms_permission($request)) {
            $message = _x('Sorry, you are not allowed to assign the provided terms.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_cannot_assign_term', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    protected function has_assign_terms_permission(\WP_REST_Request $request): bool
    {
        $terms = Arr::consolidate($request['assigned_terms']);
        foreach ($terms as $termId) {
            if (!get_term($termId, glsr()->taxonomy)) {
                continue; // Invalid terms will be rejected later
            }
            if (!current_user_can('assign_term', (int) $termId)) {
                return false;
            }
        }
        return true;
    }

    protected function has_edit_others_permission(\WP_REST_Request $request): bool
    {
        if (empty($request['author'])) {
            return true;
        }
        if (get_current_user_id() === $request['author']) {
            return true;
        }
        if (glsr()->can('edit_others_posts')) {
            return true;
        }
        return false;
    }

    protected function has_read_permission(Review $review): bool
    {
        if (!is_user_logged_in()) {
            return false;
        }
        if(!$review->is_approved && !glsr()->can('read_post', $review->ID)) {
            return false;
        }
        return true;
    }
}
