<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\SendVerificationEmail;
use GeminiLabs\SiteReviews\Commands\TogglePinned;
use GeminiLabs\SiteReviews\Commands\ToggleStatus;
use GeminiLabs\SiteReviews\Commands\ToggleVerified;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

/**
 * The parts of a review the review list and the review editor set (status, pinned, verified,
 * a verification request), and the parts of the collection the Tools page acts on.
 */
class RestReviewPartsController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkEditPermission(\WP_REST_Request $request)
    {
        $review = $this->review($request);
        if (!$review->isValid()) {
            return $this->refuse('invalid_review', _x('Invalid review ID.', 'admin-text', 'site-reviews'), 404);
        }
        return $this->permission('edit_post', $review->ID);
    }

    /**
     * @return true|\WP_Error
     */
    public function checkPublishPermission(\WP_REST_Request $request)
    {
        $review = $this->review($request);
        if (!$review->isValid()) {
            return $this->refuse('invalid_review', _x('Invalid review ID.', 'admin-text', 'site-reviews'), 404);
        }
        return $this->permission('publish_post', $review->ID);
    }

    /**
     * @return true|\WP_Error
     */
    public function checkVerifiedPermission(\WP_REST_Request $request)
    {
        $permission = $this->checkEditPermission($request);
        if (is_wp_error($permission)) {
            return $permission;
        }
        if (!glsr()->filterBool('verification/enabled', false)) {
            return $this->refuse('verification_disabled',
                _x('Verification is not enabled on this site.', 'admin-text', 'site-reviews'),
                403
            );
        }
        return true;
    }

    public function registerRoutes(): void
    {
        $id = '(?P<id>\d+)';
        register_rest_route($this->restNamespace(), "/reviews/{$id}/pinned", [
            [
                'args' => [
                    'value' => [
                        'required' => true,
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'setPinned'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkEditPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), "/reviews/{$id}/status", [
            [
                'args' => [
                    'status' => [
                        'enum' => ['pending', 'publish'],
                        'required' => true,
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'setStatus'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkPublishPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), "/reviews/{$id}/verification-requests", [
            [
                'callback' => [$this, 'requestVerification'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkEditPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), "/reviews/{$id}/verified", [
            [
                'args' => [
                    'value' => [
                        'required' => true,
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'setVerified'],
                'methods' => 'PUT',
                'permission_callback' => [$this, 'checkVerifiedPermission'],
            ],
        ]);
    }

    public function requestVerification(\WP_REST_Request $request): \WP_REST_Response
    {
        $review = $this->review($request);
        $pathPostId = $review->meta()->cast('_submitted._post_id', 'int');
        $path = Cast::toString(wp_parse_url((string) get_permalink($pathPostId), \PHP_URL_PATH));
        return $this->handle(new SendVerificationEmail($review, $review->verifyUrl($path)));
    }

    public function setPinned(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new TogglePinned(new Request([
            'pinned' => (int) $request['value'],
            'post_id' => (int) $request['id'],
        ])));
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function setStatus(\WP_REST_Request $request)
    {
        require_once \ABSPATH.'wp-admin/includes/class-wp-list-table.php';
        require_once \ABSPATH.'wp-admin/includes/class-wp-posts-list-table.php';
        require_once \ABSPATH.'wp-admin/includes/class-wp-screen.php';
        require_once \ABSPATH.'wp-admin/includes/post.php';
        require_once \ABSPATH.'wp-admin/includes/screen.php';
        require_once \ABSPATH.'wp-admin/includes/template.php';
        try {
            return $this->handle(new ToggleStatus(new Request([
                'post_id' => (int) $request['id'],
                'status' => $request['status'],
            ])));
        } catch (\Throwable $e) {
            glsr_log()->error($e->getMessage());
            return $this->refuse('status_failed', _x('The status could not be changed.', 'admin-text', 'site-reviews'), 500);
        }
    }

    public function setVerified(\WP_REST_Request $request): \WP_REST_Response
    {
        return $this->handle(new ToggleVerified(new Request([
            'post_id' => (int) $request['id'],
            'verified' => (int) $request['value'],
        ])));
    }

    protected function review(\WP_REST_Request $request): Review
    {
        return glsr(ReviewManager::class)->get((int) $request['id']);
    }
}
