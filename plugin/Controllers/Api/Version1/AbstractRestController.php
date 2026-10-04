<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Mutex;
use GeminiLabs\SiteReviews\Request;

/**
 * Base class for the plugin's own REST routes. It does not extend WP_REST_Controller:
 * these routes return command and query results directly, so the resource machinery
 * (context/field filtering, additional fields) does not apply. The reviews CRUD
 * controller stays on WP_REST_Controller because core's rest_controller_class
 * accepts only that subclass.
 */
abstract class AbstractRestController
{
    abstract public function registerRoutes(): void;

    /**
     * The fields that a form posted to a route. The route determines the action, so
     * the submitted _action value is overwritten. The captcha token is injected as
     * it is in Request::inputPost().
     */
    protected function formRequest(\WP_REST_Request $request, string $action): Request
    {
        $values = Arr::consolidate($request->get_param(glsr()->id));
        $values['_action'] = $action;
        if (in_array($action, glsr(Captcha::class)->actions())) {
            $tokenField = Cast::toString(Arr::get(glsr(Captcha::class)->config(), 'token_field'));
            if ('' !== $tokenField) {
                $values['_captcha'] = Cast::toString($request->get_param($tokenField));
            }
        }
        return new Request($values);
    }

    /**
     * The same lock that the Router takes for an action (Modules\Mutex).
     *
     * @return true|\WP_Error
     */
    protected function lock(string $action)
    {
        if (!glsr(Mutex::class)->isValid($action)) {
            return $this->refuse('too_many_requests',
                __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'),
                429
            );
        }
        return true;
    }

    /**
     * What a permission callback returns to refuse a request. The script reads an
     * error code that begins with glsr_ as the answer of the route. A callback that
     * returns false is answered with rest_forbidden, which the script reads as a
     * blocked REST API: it then sends the request again over admin-ajax.
     */
    protected function refuse(string $code, string $message, int $status = 403): \WP_Error
    {
        return new \WP_Error(glsr()->prefix.$code, $message, ['status' => $status]);
    }

    protected function respond(array $data, int $status = 200): \WP_REST_Response
    {
        return new \WP_REST_Response($data, $status);
    }

    protected function restNamespace(): string
    {
        return glsr()->id.'/v1';
    }
}
