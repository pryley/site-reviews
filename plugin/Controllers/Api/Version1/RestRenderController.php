<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Commands\FetchPagedReviews;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Request;

class RestRenderController extends AbstractRestController
{
    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/render/reviews', [
            [
                'args' => [
                    'atts' => [
                        'default' => [],
                        'description' => 'The shortcode attributes of the rendered reviews.',
                        'type' => 'object',
                    ],
                    'page' => [
                        'default' => 1,
                        'minimum' => 1,
                        'type' => 'integer',
                    ],
                    'schema' => [
                        'default' => false,
                        'type' => 'boolean',
                    ],
                    'url' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'renderReviews'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
            ],
        ]);
        register_rest_route($this->restNamespace(), '/render/reviews/(?P<id>[\d]+)', [
            [
                'args' => [
                    'form' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'id' => [
                        'type' => 'integer',
                    ],
                    'theme' => [
                        'default' => '',
                        'type' => 'string',
                    ],
                    'verified' => [
                        'default' => '',
                        'description' => 'The encrypted verification token from the review verification redirect.',
                        'type' => 'string',
                    ],
                ],
                'callback' => [$this, 'renderReview'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => '__return_true',
            ],
        ]);
    }

    /**
     * Renders a single review after the submit redirect (approved) or the
     * verification redirect (verified).
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function renderReview(\WP_REST_Request $request)
    {
        $reviewId = (int) $request['id'];
        $token = (string) $request['verified'];
        $isVerification = '' !== $token;
        if ($isVerification && $reviewId !== (int) glsr(Encryption::class)->decrypt($token)) {
            return new \WP_Error('glsr_invalid_token',
                __('The review verification token is invalid.', 'site-reviews'),
                ['status' => 400]
            );
        }
        $review = glsr_get_review($reviewId);
        if (!$review->isValid() || (!$isVerification && !$review->is_approved)) {
            return new \WP_Error('glsr_review_not_found',
                __('The review does not exist.', 'site-reviews'),
                ['status' => 404]
            );
        }
        $html = $review->build([
            'form' => (string) $request['form'],
            'review_id' => $reviewId,
            'theme' => (string) $request['theme'],
            'verified' => $token,
        ]);
        $data = [
            'attributes' => $html->attributes(),
            'review' => (string) $html,
        ];
        if ($isVerification) {
            $data['message'] = $review->is_approved
                ? __('Thank you, your review has been verified.', 'site-reviews')
                : __('Thank you, your review has been verified and is awaiting approval.', 'site-reviews');
        }
        return $this->respond($data);
    }

    public function renderReviews(\WP_REST_Request $request): \WP_REST_Response
    {
        $command = new FetchPagedReviews(new Request([
            'atts' => (array) $request['atts'],
            'page' => (int) $request['page'],
            'schema' => (bool) $request['schema'],
            'url' => (string) $request['url'],
        ]));
        $command->handle();
        return $this->respond($command->response());
    }
}
