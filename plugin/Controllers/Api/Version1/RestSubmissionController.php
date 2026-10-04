<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\CreateReview;

class RestSubmissionController extends AbstractRestController
{
    /**
     * @return true|\WP_Error
     */
    public function checkSubmissionPermission(\WP_REST_Request $request)
    {
        return $this->lock('submit-review');
    }

    public function createSubmission(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new CreateReview($this->formRequest($request, 'submit-review'));
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
}
