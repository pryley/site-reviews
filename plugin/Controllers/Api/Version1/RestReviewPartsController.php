<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\GeolocateReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviews;
use GeminiLabs\SiteReviews\Commands\ImportReviewsAttachments;
use GeminiLabs\SiteReviews\Commands\ImportReviewsCleanup;
use GeminiLabs\SiteReviews\Commands\ProcessCsvFile;
use GeminiLabs\SiteReviews\Commands\RemoveLocationData;
use GeminiLabs\SiteReviews\Commands\RepairReviewRelations;
use GeminiLabs\SiteReviews\Commands\ResetAssignedMeta;
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
    public function checkToolsPermission()
    {
        return $this->permission(glsr()->getPermission('tools', 'general'));
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

    public function geolocateReviews(): \WP_REST_Response
    {
        return $this->handle(new GeolocateReviews());
    }

    /**
     * @return \WP_REST_Response|\WP_Error
     */
    public function import(\WP_REST_Request $request)
    {
        $stages = $this->importStages($request['source']);
        if (empty($stages[$request['stage']])) {
            return $this->refuse('invalid_import_source', _x('Nothing can be imported from this source.', 'admin-text', 'site-reviews'), 404);
        }
        require_once \ABSPATH.'wp-admin/includes/file.php';
        require_once \ABSPATH.'wp-admin/includes/image.php';
        require_once \ABSPATH.'wp-admin/includes/media.php'; // stage 3 saves attachments
        $values = $request->get_params();
        $values['alt'] = $request['revert'];
        try {
            return $this->handle(new $stages[$request['stage']](new Request($values)));
        } catch (\Throwable $e) {
            glsr_log()->error($e->getMessage());
            return $this->refuse('import_failed', _x('The import failed.', 'admin-text', 'site-reviews'), 500);
        }
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/reviews/counts', [
            [
                'callback' => [$this, 'resetCounts'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/reviews/import', [
            [
                'args' => [
                    'revert' => [
                        'default' => false,
                        'type' => 'boolean',
                    ],
                    'source' => [
                        'default' => 'csv',
                        'pattern' => '^[a-z-]+$',
                        'type' => 'string',
                    ],
                    'stage' => [
                        'maximum' => 4,
                        'minimum' => 1,
                        'required' => true,
                        'type' => 'integer',
                    ],
                ],
                'callback' => [$this, 'import'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkToolsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/reviews/locations', [
            [
                'callback' => [$this, 'geolocateReviews'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkToolsPermission'],
            ],
            [
                'callback' => [$this, 'removeLocations'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'checkToolsPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/reviews/relations', [
            [
                'args' => [
                    'invalid' => [
                        'enum' => [true], // only the invalid relations are removed
                        'required' => true,
                        'type' => 'boolean',
                    ],
                ],
                'callback' => [$this, 'removeInvalidRelations'],
                'methods' => \WP_REST_Server::DELETABLE,
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
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

    public function removeInvalidRelations(): \WP_REST_Response
    {
        return $this->handle(new RepairReviewRelations());
    }

    public function removeLocations(): \WP_REST_Response
    {
        return $this->handle(new RemoveLocationData());
    }

    public function requestVerification(\WP_REST_Request $request): \WP_REST_Response
    {
        $review = $this->review($request);
        $pathPostId = $review->meta()->cast('_submitted._post_id', 'int');
        $path = Cast::toString(wp_parse_url((string) get_permalink($pathPostId), \PHP_URL_PATH));
        return $this->handle(new SendVerificationEmail($review, $review->verifyUrl($path)));
    }

    public function resetCounts(): \WP_REST_Response
    {
        return $this->handle(new ResetAssignedMeta());
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

    /**
     * @return array<int, class-string>
     */
    protected function importStages(string $source): array
    {
        $stages = [];
        if ('csv' === $source) {
            $stages = [
                1 => ProcessCsvFile::class,
                2 => ImportReviews::class,
                3 => ImportReviewsAttachments::class,
                4 => ImportReviewsCleanup::class,
            ];
        }
        return glsr()->filterArray('import/stages', $stages, $source);
    }

    protected function review(\WP_REST_Request $request): Review
    {
        return glsr(ReviewManager::class)->get((int) $request['id']);
    }
}
