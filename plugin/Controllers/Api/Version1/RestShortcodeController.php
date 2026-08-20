<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Defaults\ShortcodeApiFetchDefaults;

class RestShortcodeController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkShortcodePermission(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            $error = _x('Sorry, you are not allowed to do that.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $error, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        if (!glsr()->shortcode($request['shortcode'])) {
            return new \WP_Error('rest_invalid_shortcode', 'Invalid shortcode', [
                'status' => 400,
            ]);
        }
        return true;
    }

    public function fetchOptions(\WP_REST_Request $request): \WP_REST_Response
    {
        $args = glsr(ShortcodeApiFetchDefaults::class)->merge($request->get_params());
        $results = [];
        if (!empty($args['option'])) {
            $manager = glsr(ShortcodeOptionManager::class);
            $values = call_user_func([$manager, $args['option']], $args);
            foreach ($values as $id => $title) {
                $results[] = compact('id', 'title');
            }
        }
        return $this->respond($results);
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/shortcode/(?P<shortcode>[a-z_]+)', [
            [
                'callback' => [$this, 'fetchOptions'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkShortcodePermission'],
            ],
        ]);
    }
}
