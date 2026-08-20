<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Mutex;
use GeminiLabs\SiteReviews\Request;

class RestSubmissionController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkSubmissionPermission(\WP_REST_Request $request)
    {
        if (!glsr(Mutex::class)->isValid('submit-review')) {
            return new \WP_Error('glsr_too_many_requests',
                __('The form could not be submitted. Please notify the site administrator.', 'site-reviews'),
                ['status' => 429]
            );
        }
        return true;
    }

    public function createSubmission(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new CreateReview($this->submissionRequest($request));
        $command->handle();
        return $this->respond($command->response(), $command->successful() ? 201 : 400);
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/submissions', [
            [
                'args' => [
                    glsr()->id => [
                        'description' => 'The fields submitted by the review form.',
                        'required' => true,
                        'type' => 'object',
                    ],
                ],
                'callback' => [$this, 'createSubmission'],
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => [$this, 'checkSubmissionPermission'],
            ],
        ]);
    }

    /**
     * The route determines the action, so the submitted _action value is overwritten.
     * The captcha token is injected as it is in Request::inputPost().
     */
    protected function submissionRequest(\WP_REST_Request $request): Request
    {
        $values = Arr::consolidate($request->get_param(glsr()->id));
        $values['_action'] = 'submit-review';
        if (in_array('submit-review', glsr(Captcha::class)->actions())) {
            $tokenField = Cast::toString(Arr::get(glsr(Captcha::class)->config(), 'token_field'));
            if ('' !== $tokenField) {
                $values['_captcha'] = Cast::toString($request->get_param($tokenField));
            }
        }
        return new Request($values);
    }
}
