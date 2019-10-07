<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Notice;

class Router
{
    /**
     * @var array
     */
    protected $unguardedActions = [];

    public function __construct()
    {
        $this->unguardedActions = apply_filters('site-reviews/router/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
        ]);
    }

    /**
     * @return void
     */
    public function routeAdminPostRequest()
    {
        $request = $this->getRequest();
        if (!$this->isValidPostRequest($request)) {
            return;
        }
        check_admin_referer($request['_action']);
        $this->routeRequest('admin', $request['_action'], $request);
    }

    /**
     * @return void
     */
    public function routeAjaxRequest()
    {
        $request = $this->getRequest();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request);
        $this->routeRequest('ajax', $request['_action'], $request);
        wp_die();
    }

    /**
     * @return void
     */
    public function routePublicPostRequest()
    {
        if (is_admin()) {
            return;
        }
        $request = $this->getRequest();
        if (!$this->isValidPostRequest($request)) {
            return;
        }
        if (!$this->isValidPublicNonce($request)) {
            return;
        }
        $this->routeRequest('public', $request['_action'], $request);
    }

    /**
     * @return void
     */
    protected function checkAjaxNonce(array $request)
    {
        if (!is_user_logged_in() || in_array(Arr::get($request, '_action'), $this->unguardedActions)) {
            return;
        }
        if (!isset($request['_nonce'])) {
            $this->sendAjaxError('request is missing a nonce', $request);
        }
        if (!wp_verify_nonce($request['_nonce'], $request['_action'])) {
            $this->sendAjaxError('request failed the nonce check', $request, 403);
        }
    }

    /**
     * @return void
     */
    protected function checkAjaxRequest(array $request)
    {
        if (!isset($request['_action'])) {
            $this->sendAjaxError('request must include an action', $request);
        }
        if (empty($request['_ajax_request'])) {
            $this->sendAjaxError('request is invalid', $request);
        }
    }

    /**
     * All ajax requests in the plugin are triggered by a single action hook: glsr_action,
     * while each ajax route is determined by $_POST[request][_action].
     * @return array
     */
    protected function getRequest()
    {
        $request = Helper::filterInputArray(Application::ID);
        if (Helper::filterInput('action') == Application::PREFIX.'action') {
            $request['_ajax_request'] = true;
        }
        if ('submit-review' == Helper::filterInput('_action', $request)) {
            $request['_recaptcha-token'] = Helper::filterInput('g-recaptcha-response');
        }
        return $request;
    }

    /**
     * @return bool
     */
    protected function isValidPostRequest(array $request = [])
    {
        return !empty($request['_action']) && empty($request['_ajax_request']);
    }

    /**
     * @return bool
     */
    protected function isValidPublicNonce(array $request)
    {
        if (is_user_logged_in() && !wp_verify_nonce($request['_nonce'], $request['_action'])) {
            glsr_log()->error('nonce check failed for public request')->debug($request);
            return false;
        }
        return true;
    }

    /**
     * @param string $type
     * @param string $action
     * @return void
     */
    protected function routeRequest($type, $action, array $request = [])
    {
        $actionHook = 'site-reviews/route/'.$type.'/request';
        $controller = glsr(Helper::buildClassName($type.'-controller', 'Controllers'));
        $method = Helper::buildMethodName($action, 'router');
        $request = apply_filters('site-reviews/route/request', $request, $action, $type);
        do_action($actionHook, $action, $request);
        if (is_callable([$controller, $method])) {
            call_user_func([$controller, $method], $request);
            return;
        }
        if (0 === did_action($actionHook)) {
            glsr_log('Unknown '.$type.' router request: '.$action);
        }
    }

    /**
     * @param string $error
     * @param int $statusCode
     * @return void
     */
    protected function sendAjaxError($error, array $request, $statusCode = 400)
    {
        glsr_log()->error($error)->debug($request);
        glsr(Notice::class)->addError(__('There was an error (try reloading the page).', 'site-reviews').' <code>'.$error.'</code>');
        wp_send_json_error([
            'message' => __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'),
            'notices' => glsr(Notice::class)->get(),
            'error' => $error,
        ]);
    }
}
