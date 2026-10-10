<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestPremiumController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestRenderController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSearchController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestShortcodeController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSubmissionController;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\RestSummaryController;
use GeminiLabs\SiteReviews\Helpers\Cast;

class RestController
{
    /**
     * The fields of an admin-ajax request that describe the REST request it carries.
     */
    public const AJAX_FIELDS = ['action', '_rest_method', '_rest_nonce', '_rest_path', '_rest_query'];

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
        $path = trim(Cast::toString($post['_rest_path'] ?? ''), '/');
        wp_parse_str(Cast::toString($post['_rest_query'] ?? ''), $query);
        $request = new \WP_REST_Request($method, sprintf('/%s/v1/%s', glsr()->id, $path));
        $request->set_query_params($query);
        $request->set_body_params(array_diff_key($post, array_flip(static::AJAX_FIELDS)));
        $request->set_file_params($files);
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
        $response = $this->ajaxResponse(wp_unslash($_POST), $_FILES);
        wp_send_json($response->get_data(), $response->get_status());
    }

    /**
     * @action rest_api_init
     */
    public function registerRoutes(): void
    {
        (new RestPremiumController())->registerRoutes();
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
