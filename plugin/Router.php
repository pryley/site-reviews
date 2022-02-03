<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;

class Router
{
    /**
     * @var array
     */
    protected $unguardedAdminActions;

    /**
     * @var array
     */
    protected $unguardedPublicActions;

    public function __construct()
    {
        // Authenticated routes to unguard
        $this->unguardedAdminActions = glsr()->filterArray('router/admin/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
        ]);
        // Unauthenticated routes to unguard
        $this->unguardedPublicActions = glsr()->filterArray('router/public/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
            'submit-review',
        ]);
    }

    /**
     * @return void
     */
    public function routeAdminAjaxRequest()
    {
        $request = $this->getRequest();
        $this->checkAjaxRequest($request);
        if (!in_array($request->_action, $this->unguardedAdminActions)) {
            $this->checkAjaxNonce($request);
        }
        $this->routeRequest('ajax', $request);
        wp_die();
    }

    /**
     * @return void
     */
    public function routeAdminPostRequest()
    {
        $request = $this->getRequest();
        if ($this->isValidPostRequest($request)) {
            check_admin_referer($request->_action); // die() called if nonce is invalid
            $this->routeRequest('admin', $request);
        }
    }

    /**
     * @return void
     */
    public function routePublicAjaxRequest()
    {
        $request = $this->getRequest();
        $this->checkAjaxRequest($request);
        if (!in_array($request->_action, $this->unguardedPublicActions)) {
            $this->checkAjaxNonce($request);
        }
        $this->routeRequest('ajax', $request);
        wp_die();
    }

    /**
     * @return void
     */
    public function routePublicPostRequest()
    {
        if (glsr()->isAdmin()) {
            return;
        }
        $request = $this->getRequest();
        if ($this->isValidPostRequest($request) && $this->isValidPublicNonce($request)) {
            $this->routeRequest('public', $request);
        }
    }

    /**
     * @return void
     */
    protected function checkAjaxNonce(Request $request)
    {
        if (empty($request->_nonce)) {
            $this->sendAjaxError('AJAX request is missing a nonce', $request, 400, 'Unauthorized request');
        }
        if (!wp_verify_nonce($request->_nonce, $request->_action)) {
            $this->sendAjaxError('AJAX request failed the nonce check', $request, 403, 'Unauthorized request');
        }
    }

    /**
     * @return void
     */
    protected function checkAjaxRequest(Request $request)
    {
        if (empty($request->_action)) {
            $this->sendAjaxError('AJAX request must include an action', $request, 400, 'Invalid request');
        }
        if (empty($request->_ajax_request)) {
            $this->sendAjaxError('AJAX request is invalid', $request, 400, 'Invalid request');
        }
    }

    /**
     * All ajax requests in the plugin are triggered by a single action hook: glsr_action,
     * while each ajax route is determined by $_POST[request][_action].
     * @return Request
     */
    protected function getRequest()
    {
        $request = Helper::filterInputArray(glsr()->id);
        if (Helper::filterInput('action') == glsr()->prefix.'action') {
            $request['_ajax_request'] = true;
        }
        if ('submit-review' == Helper::filterInput('_action', $request)) {
            $request['_recaptcha-token'] = Helper::filterInput('g-recaptcha-response');
        }
        return new Request($request);
    }

    /**
     * @return bool
     */
    protected function isValidPostRequest(Request $request)
    {
        return !empty($request->_action) && empty($request->_ajax_request);
    }

    /**
     * @return bool
     */
    protected function isValidPublicNonce(Request $request)
    {
        // only require a nonce for public requests if user is logged in, this avoids 
        // potential caching issues since unauthenticated requests should never be destructive.
        if (is_user_logged_in() && !wp_verify_nonce($request->_nonce, $request->_action)) {
            glsr_log()->warning('nonce check failed for public request')->debug($request);
            return false;
        }
        return true;
    }

    /**
     * @param string $type
     * @return void
     */
    protected function routeRequest($type, Request $request)
    {
        $actionHook = "route/{$type}/{$request->_action}";
        $request = glsr()->filterArray('route/request', $request->toArray(), $request->_action, $type);
        $request = new Request($request);
        glsr()->action($actionHook, $request);
        if (0 === did_action(glsr()->id.'/'.$actionHook)) {
            glsr_log()->warning('Unknown '.$type.' router request: '.$request->_action);
        }
    }

    /**
     * @param string $error
     * @param int $code
     * @param string $message
     * @return void
     */
    protected function sendAjaxError($error, Request $request, $code = 400, $message = '')
    {
        glsr_log()->error($error)->debug($request);
        $notices = '';
        if (glsr()->isAdmin()) {
            glsr(Notice::class)->addError(_x('There was an error (try reloading the page).', 'admin-text', 'site-reviews').' <code>'.$error.'</code>');
            $notices = glsr(Notice::class)->get();
        }
        if ('submit-review' === $request->_action) {
            $message = __('The form could not be submitted. Please notify the site administrator.', 'site-reviews');
        }
        wp_send_json_error([
            'code' => $code,
            'error' => $error,
            'message' => $message ?: $error,
            'notices' => $notices,
        ]);
    }
}
