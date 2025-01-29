<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Defaults\ShortcodeApiFetchDefaults;

class RestShortcodeController extends \WP_REST_Controller
{
    public function __construct()
    {
        $this->namespace = glsr()->id.'/v1';
        $this->rest_base = 'shortcode';
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function get_items($request)
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
        return rest_ensure_response($results);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return true|\WP_Error
     */
    public function get_items_permissions_check($request)
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

    /**
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, "/{$this->rest_base}/(?P<shortcode>[a-z_]+)", [
            [
                'callback' => [$this, 'get_items'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'get_items_permissions_check'],
            ],
        ]);
    }
}
