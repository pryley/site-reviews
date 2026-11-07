<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;

/**
 * The options of a shortcode's editor controls used by blocks and page builder integrations.
 */
class RestShortcodeController extends AbstractRestController
{
    /**
     * The options include the users of the site, so the route asks for the capability
     * that shows the reviews in the admin.
     *
     * @return true|\WP_Error
     */
    public function checkShortcodePermission(\WP_REST_Request $request)
    {
        $permission = $this->permission('edit_posts');
        if (is_wp_error($permission)) {
            return $permission;
        }
        if (!glsr()->shortcode($request['shortcode'])) {
            return $this->refuse('invalid_shortcode', _x('Invalid shortcode.', 'admin-text', 'site-reviews'), 404);
        }
        return true;
    }

    public function fetchOptions(\WP_REST_Request $request): \WP_REST_Response
    {
        $results = [];
        $values = glsr(ShortcodeOptionManager::class)->get((string) $request['option'], $request->get_params());
        foreach ($values as $id => $title) {
            $results[] = compact('id', 'title');
        }
        return $this->respond($results);
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/shortcodes/(?P<shortcode>[a-z_]+)/options/(?P<option>[a-z_]+)', [
            [
                'args' => [
                    'include' => [
                        'default' => '',
                        'type' => 'string', // ids joined by commas
                    ],
                    'search' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'fetchOptions'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkShortcodePermission'],
            ],
        ]);
    }
}
