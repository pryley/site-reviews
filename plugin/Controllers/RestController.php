<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestPluginController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestRenderController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSearchController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestShortcodeController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSubmissionController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSummaryController;
use GeminiLabs\SiteReviews\Helpers\Cast;

class RestController
{
    /**
     * The fields of an admin-ajax request that describe its REST request.
     */
    public const AJAX_FIELDS = [
        'action', '_rest_body', '_rest_method', '_rest_nonce', '_rest_path', '_rest_query',
    ];

    /**
     * The WordPress routes sent through the admin-ajax action for logged-in users.
     */
    public const WORDPRESS_ROUTES = [
        '/wp/v2/users/me',
    ];

    /**
     * Dispatches a request for one of this plugin's REST routes that arrived over admin-ajax.
     *
     * @param array $post  The unslashed POST fields
     * @param array $files $_FILES
     */
    public function ajaxResponse(array $post, array $files = []): \WP_REST_Response
    {
        $error = $this->cookieError(Cast::toString($post['_rest_nonce'] ?? ''));
        if (is_wp_error($error)) {
            return rest_convert_error_to_response($error);
        }
        $method = strtoupper(Cast::toString($post['_rest_method'] ?? '')) ?: 'GET';
        $path = Cast::toString($post['_rest_path'] ?? '');
        $route = str_starts_with($path, '/')
            ? '/'.trim($path, '/') // from the REST root, such as /wp/v2/users/me
            : sprintf('/%s/v1/%s', glsr()->id, trim($path, '/'));
        if (!str_starts_with($route, sprintf('/%s/v1/', glsr()->id))) {
            if (!in_array($route, static::WORDPRESS_ROUTES, true)) {
                return rest_convert_error_to_response(new \WP_Error('rest_no_route', __('No route was found matching the URL and request method.'), ['status' => 404])); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
            }
            if (!is_user_logged_in()) {
                return rest_convert_error_to_response(new \WP_Error('rest_not_logged_in', __('You are not currently logged in.'), ['status' => 401])); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
            }
            unset($_REQUEST['_wpnonce']);
            if (!empty($post['_rest_nonce'])) {
                $_REQUEST['_wpnonce'] = Cast::toString($post['_rest_nonce']); // where rest_cookie_check_errors() reads the nonce
            }
            $authentication = rest_get_server()->check_authentication();
            if (is_wp_error($authentication)) {
                return rest_convert_error_to_response($authentication);
            }
        }
        wp_parse_str(Cast::toString($post['_rest_query'] ?? ''), $query);
        $request = new \WP_REST_Request($method, $route);
        $request->set_query_params($query);
        $request->set_body_params(array_diff_key($post, array_flip(static::AJAX_FIELDS)));
        $request->set_file_params($files);
        if (isset($post['_rest_body'])) { // a JSON body
            $request->set_header('Content-Type', 'application/json');
            $request->set_body(Cast::toString($post['_rest_body']));
        }
        // As rest_preload_api_request() does for a request that WordPress dispatches itself.
        $server = rest_get_server();
        $response = rest_do_request($request);
        $response = apply_filters('rest_post_dispatch', rest_ensure_response($response), $server, $request);
        $response->set_data($server->response_to_data($response, false));
        return $response;
    }

    /**
     * @action wp_ajax_glsr_rest_request
     * @action wp_ajax_nopriv_glsr_rest_request
     */
    public function dispatchAjaxRequest(): void
    {
        $response = $this->ajaxResponse(wp_unslash($_POST), $_FILES); // as WP_REST_Server::serve_request() reads them
        wp_send_json($response->get_data(), $response->get_status());
    }

    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestPluginController())->registerRoutes();
        (new RestRenderController())->registerRoutes();
        (new RestSearchController())->registerRoutes();
        (new RestShortcodeController())->registerRoutes();
        (new RestSubmissionController())->registerRoutes();
        (new RestSummaryController())->registerRoutes();
    }

    protected function cookieError(string $nonce): ?\WP_Error
    {
        if (!is_user_logged_in()) {
            return null;
        }
        if ('' === $nonce) {
            wp_set_current_user(0);
            return null;
        }
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new \WP_Error('rest_cookie_invalid_nonce', __('Cookie check failed'), ['status' => 403]); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain
        }
        return null;
    }
}
