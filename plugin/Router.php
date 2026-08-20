<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\Modules\Mutex;
use GeminiLabs\SiteReviews\Modules\Notice;

class Router implements ControllerContract
{
    use HookProxy;

    /**
     * @action wp_ajax_glsr_admin_action
     */
    public function routeAdminAjaxRequest(): void
    {
        $request = Request::inputPost();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request, 'admin');
        $this->checkMutexRequest($request);
        $this->post('ajax', $request);
        wp_die();
    }

    /**
     * A routed admin GET request will look like this: /wp-admin/?glsr_=.
     *
     * @action admin_init
     */
    public function routeAdminGetRequest(): void
    {
        $request = Request::inputGet();
        if (!empty($request->action)) {
            $this->get('admin', $request);
        }
    }

    /**
     * @action admin_init
     */
    public function routeAdminPostRequest(): void
    {
        $request = Request::inputPost();
        if (!$this->isValidRequest($request)) {
            return;
        }
        check_admin_referer($request->_action); // die() called if nonce is invalid, assumes _wpnonce
        if (!$this->isValidMutexRequest($request)) {
            return;
        }
        $this->post('admin', $request);
    }

    /**
     * @action wp_ajax_glsr_public_action
     * @action wp_ajax_nopriv_glsr_public_action
     */
    public function routePublicAjaxRequest(): void
    {
        $request = Request::inputPost();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request, 'public');
        $this->checkMutexRequest($request);
        $this->post('ajax', $request);
        wp_die();
    }

    /**
     * A routed public GET request will look like this: ?glsr_=.
     *
     * @action parse_request
     */
    public function routePublicGetRequest(): void
    {
        $request = Request::inputGet();
        if (!empty($request->action)) {
            $this->get('public', $request);
        }
    }

    /**
     * @action init
     */
    public function routePublicPostRequest(): void
    {
        if (glsr()->isAdmin()) {
            return;
        }
        if ($this->isRestRequest()) {
            return; // Api\Version1 owns REST submissions; see isRestRequest()
        }
        $request = Request::inputPost();
        if (!$this->isValidRequest($request)) {
            return;
        }
        if (!$this->isValidPublicNonce($request)) {
            return;
        }
        if (!$this->isValidMutexRequest($request)) {
            return;
        }
        $this->post('public', $request);
    }

    protected function checkAjaxNonce(Request $request, string $type): void
    {
        $unguardedActions = 'admin' === $type
            ? $this->unguardedAdminActions()
            : $this->unguardedPublicActions();
        if (in_array($request->_action, $unguardedActions)) {
            return;
        }
        if (empty($request->_nonce)) {
            $this->sendAjaxError('AJAX request is missing a nonce', $request, 400, 'Unauthorized request');
        }
        if (!wp_verify_nonce($request->_nonce, $request->_action)) {
            $this->sendAjaxError('AJAX request failed the nonce check', $request, 403, 'Unauthorized request');
        }
    }

    protected function checkAjaxRequest(Request $request): void
    {
        if (empty($request->_action)) {
            $this->sendAjaxError('AJAX request must include an action', $request, 400, 'Bad Request');
        }
        if (empty($request->_ajax_request)) {
            $this->sendAjaxError('AJAX request is invalid', $request, 400, 'Bad Request');
        }
    }

    protected function checkMutexRequest(Request $request): void
    {
        if (!$this->isValidMutexRequest($request)) {
            $this->sendAjaxError('Parallel AJAX request (possible single-packet attack)', $request, 429, 'Too Many Requests');
        }
    }

    protected function get(string $type, Request $request): void
    {
        $hook = "route/get/{$type}/{$request->action}";
        glsr()->action('route/request', $request, $hook);
        if (!$this->isRouted($hook)) {
            glsr_log()->warning("Unknown {$type} router GET request: {$request->action}");
            return;
        }
        glsr()->action($hook, $request);
    }

    /**
     * Whether anything is listening on a route.
     *
     * It is asked AFTER the route/request action has fired, so that an addon which
     * registers its own route from there still counts as a listener.
     */
    protected function isRouted(string $hook): bool
    {
        return false !== has_action(glsr()->id."/{$hook}");
    }

    /**
     * Whether the request targets the REST API. This route runs on `init`, which fires
     * before `parse_request` defines REST_REQUEST, so wp_is_serving_rest_request() cannot
     * answer yet. A REST review submission carries the form's own _action field; without
     * this check the no-JS fallback route would consume it before the REST server dispatches.
     */
    protected function isRestRequest(): bool
    {
        if (!empty(filter_input(\INPUT_GET, 'rest_route'))) {
            return true; // plain permalinks
        }
        // Plain permalinks: the REST API is only addressable with ?rest_route=,
        // and rest_url() is home_url('index.php?rest_route=/') — its path would
        // classify every /index.php request as REST.
        if (!get_option('permalink_structure')) {
            return false;
        }
        $restPath = (string) parse_url(rest_url(), \PHP_URL_PATH);
        if (in_array($restPath, ['', '/'])) {
            return false; // a filtered rest_url with no path: only ?rest_route= reaches the REST API
        }
        $requestPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), \PHP_URL_PATH);
        return str_starts_with($requestPath, $restPath);
    }

    protected function isValidMutexRequest(Request $request): bool
    {
        return glsr(Mutex::class)->isValid((string) $request->_action);
    }

    protected function isValidPublicNonce(Request $request): bool
    {
        // only require a nonce for public requests if user is logged in, this avoids
        // potential caching issues since unauthenticated requests should nenever be destructive.
        if (is_user_logged_in() && !wp_verify_nonce($request->_nonce, $request->_action)) {
            glsr_log()->warning('nonce check failed for public request')->debug($request);
            return false;
        }
        return true;
    }

    protected function isValidRequest(Request $request): bool
    {
        return !empty($request->_action) && empty($request->_ajax_request);
    }

    protected function post(string $type, Request $request): void
    {
        $hook = "route/{$type}/{$request->_action}";
        glsr()->action('route/request', $request, $hook);
        if (!$this->isRouted($hook)) {
            glsr_log()->warning("Unknown {$type} router POST request: {$request->_action}");
            return;
        }
        glsr()->action($hook, $request);
    }

    protected function sendAjaxError(string $error, Request $request, int $errCode, string $message): void
    {
        $data = [
            'code' => $errCode,
            'error' => $error,
            'message' => $message ?: $error,
            'notices' => '',
        ];
        if ('submit-review' === $request->_action) {
            $data['message'] = __('The form could not be submitted. Please notify the site administrator.', 'site-reviews');
        }
        if (glsr()->prefix.'admin_action' === Helper::filterInput('action')) {
            $message = _x('There was an error', 'admin-text', 'site-reviews');
            $advice = sprintf(
                /* translators: %s: a button with the text "reloading" */
                _x('Try %s the page.', 'try reloading the page (admin-text)', 'site-reviews'),
                sprintf('<button type="button" class="button-link" onclick="location.reload()">%s</button>', _x('reloading', '(admin-text) e.g. try reloading the page', 'site-reviews')),
            );
            glsr(Notice::class)->addError("{$message}: <code>{$error}</code>, {$advice}");
            $data['notices'] = glsr(Notice::class)->get();
        }
        if (429 !== $errCode) {
            glsr_log()->debug($request->toArray());
        }
        wp_send_json_error($data);
    }

    protected function unguardedAdminActions(): array
    {
        return glsr()->filterArray('router/admin/unguarded-actions', [
            'dismiss-notice',
        ]);
    }

    protected function unguardedPublicActions(): array
    {
        return glsr()->filterArray('router/public/unguarded-actions', [
            'approved-review',
            'fetch-paged-reviews',
            'submit-review',
            'verified-review',
        ]);
    }
}
