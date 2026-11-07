<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Modules\Translation;

/**
 * The searches in the admin (views/partials/listtable/filter.php), review editor's
 * assigned posts and users, and Strings settings.
 */
class RestSearchController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkSearchPermission()
    {
        return $this->permission('edit_posts');
    }

    public function registerRoutes(): void
    {
        $routes = [
            'assigned-posts' => 'searchAssignedPosts',
            'assigned-users' => 'searchAssignedUsers',
            'posts' => 'searchPosts',
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
        register_rest_route($this->restNamespace(), '/search/strings', [
            [
                'args' => [
                    'exclude' => [
                        'default' => [],
                        'items' => ['type' => 'string'],
                        'type' => 'array',
                    ],
                    'search' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'searchStrings'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkSettingsPermission'],
            ],
        ]);
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

    /**
     * The published posts a review can be assigned to.
     */
    public function searchPosts(\WP_REST_Request $request): \WP_REST_Response
    {
        $posts = glsr(Database::class)->searchPosts($this->search($request))->posts();
        return $this->respond(array_map(fn ($post) => [
            'id' => (int) $post->ID,
            'title' => Helper::ifEmpty(get_the_title($post->ID), _x('(no title)', 'admin-text', 'site-reviews')),
            'url' => (string) get_permalink($post->ID),
        ], $posts));
    }

    /**
     * The translatable strings that are not excluded or customized.
     */
    public function searchStrings(\WP_REST_Request $request): \WP_REST_Response
    {
        $exclude = array_map(fn ($id) => ['id' => $id], Arr::consolidate($request['exclude']));
        $results = glsr(Translation::class)
            ->search($this->search($request))
            ->exclude()
            ->exclude($exclude)
            ->results();
        $data = [];
        foreach ($results as $id => $entry) {
            $data[] = glsr(Translation::class)->entryData((string) $id, $entry);
        }
        return $this->respond($data);
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
            'nickname' => (string) $user->nickname,
            'title' => glsr(Sanitizer::class)->sanitizeUserName($user->name, $user->nickname),
            'url' => (string) get_author_posts_url((int) $user->id),
        ], $results);
    }
}
