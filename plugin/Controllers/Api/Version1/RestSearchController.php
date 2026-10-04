<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

/**
 * The searches of the dropdowns in the admin (views/partials/listtable/filter.php).
 */
class RestSearchController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkSearchPermission()
    {
        if (!glsr()->can('edit_posts')) {
            $error = _x('Sorry, you are not allowed to do that.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $error, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    public function registerRoutes(): void
    {
        $routes = [
            'assigned-posts' => 'searchAssignedPosts',
            'assigned-users' => 'searchAssignedUsers',
            'users' => 'searchUsers',
        ];
        foreach ($routes as $route => $callback) {
            register_rest_route($this->restNamespace(), "/search/{$route}", [
                [
                    'args' => [
                        'search' => [
                            'default' => '',
                            'type' => 'string',
                        ],
                    ],
                    'callback' => [$this, $callback],
                    'methods' => \WP_REST_Server::READABLE,
                    'permission_callback' => [$this, 'checkSearchPermission'],
                ],
            ]);
        }
    }

    /**
     * The posts that have a review assigned to them.
     */
    public function searchAssignedPosts(\WP_REST_Request $request): \WP_REST_Response
    {
        $results = glsr(Database::class)->searchAssignedPosts($this->search($request))->results();
        return $this->respond(array_map(fn ($post) => [
            'id' => (int) $post->id,
            'title' => (string) $post->name,
        ], $results));
    }

    /**
     * The users that have a review assigned to them.
     */
    public function searchAssignedUsers(\WP_REST_Request $request): \WP_REST_Response
    {
        $results = glsr(Database::class)->searchAssignedUsers($this->search($request))->results();
        return $this->respond($this->users($results));
    }

    public function searchUsers(\WP_REST_Request $request): \WP_REST_Response
    {
        $results = glsr(Database::class)->searchUsers($this->search($request))->results();
        return $this->respond($this->users($results));
    }

    protected function search(\WP_REST_Request $request): string
    {
        return glsr(Sanitizer::class)->sanitizeText($request['search']);
    }

    protected function users(array $results): array
    {
        return array_map(fn ($user) => [
            'id' => (int) $user->id,
            'title' => glsr(Sanitizer::class)->sanitizeUserName($user->name, $user->nickname),
        ], $results);
    }
}
