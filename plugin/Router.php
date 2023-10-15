<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Modules\Notice;

class Router
{
    /**
     * @action wp_ajax_glsr_action
     */
    public function routeAdminAjaxRequest(): void
    {
        $request = Request::inputPost();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request, 'admin');
        $this->post('ajax', $request);
        wp_die();
    }

    /**
     * A routed admin GET request will look like this: /wp-admin/?glsr_=.
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
        if ($this->isValidRequest($request)) {
            check_admin_referer($request->_action); // die() called if nonce is invalid, assumes _wpnonce
            $this->post('admin', $request);
        }
    }

    /**
     * @action wp_ajax_nopriv_glsr_action
     */
    public function routePublicAjaxRequest(): void
    {
        $request = Request::inputPost();
        $this->checkAjaxRequest($request);
        $this->checkAjaxNonce($request, 'public');
        $this->post('ajax', $request);
        wp_die();
    }

    /**
     * A routed public GET request will look like this: ?glsr_=.
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
        $request = Request::inputPost();
        if ($this->isValidRequest($request) && $this->isValidPublicNonce($request)) {
            $this->post('public', $request);
        }
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
            $this->sendAjaxError('AJAX request must include an action', $request, 400, 'Invalid request');
        }
        if (empty($request->_ajax_request)) {
            $this->sendAjaxError('AJAX request is invalid', $request, 400, 'Invalid request');
        }
    }

    protected function get(string $type, Request $request): void
    {
        $hook = "route/get/{$type}/{$request->action}";
        glsr()->action('route/request', $request, $hook);
        glsr()->action($hook, $request);
        if (0 === did_action(glsr()->id.'/'.$hook)) {
            glsr_log()->warning('Unknown '.$type.' router GET request: '.$request->action);
        }
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
        glsr()->action($hook, $request);
        if (0 === did_action(glsr()->id.'/'.$hook)) {
            glsr_log()->warning('Unknown '.$type.' router POST request: '.$request->_action);
        }
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
        if (glsr()->isAdmin()) {
            glsr(Notice::class)->addError(_x('There was an error (try reloading the page).', 'admin-text', 'site-reviews').' <code>'.$error.'</code>');
            $data['notices'] = glsr(Notice::class)->get();
        }
        glsr_log()->error($error)->debug($request->toArray());
        wp_send_json_error($data);
    }

    protected function unguardedAdminActions(): array
    {
        return glsr()->filterArray('router/admin/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
            'verified-review',
        ]);
    }

    protected function unguardedPublicActions(): array
    {
        return glsr()->filterArray('router/public/unguarded-actions', [
            'dismiss-notice',
            'fetch-paged-reviews',
            'submit-review',
            'verified-review',
        ]);
    }
}
