<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1;

use GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters\NormalizesArgs;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters\SummaryParameters;
use GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema\SummarySchema;
use GeminiLabs\SiteReviews\Shortcodes\SiteReviewsSummaryShortcode;

class RestSummaryController extends AbstractRestController
{
    use NormalizesArgs;

    protected ?array $schema = null;

    /**
     * @return true|\WP_Error
     */
    public function checkSummaryPermission(\WP_REST_Request $request)
    {
        if (!is_user_logged_in()) {
            $message = _x('Sorry, you are not allowed to view rating summaries.', 'admin-text', 'site-reviews');
            return new \WP_Error('rest_forbidden_context', $message, [
                'status' => rest_authorization_required_code(),
            ]);
        }
        return true;
    }

    public function fetchRating(\WP_REST_Request $request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        $ratings = glsr_get_ratings($args);
        $rendered = glsr_star_rating(
            $ratings->average,
            $ratings->reviews,
            $args
        );
        return $this->respond(
            array_merge(compact('args', 'rendered'), $ratings->toArray())
        );
    }

    public function fetchSummary(\WP_REST_Request $request): \WP_REST_Response
    {
        $args = $this->normalizedArgs($request);
        if ($request['_rendered']) {
            $args['hide'] = $request['_hide'] ?? $request['_rendered_hide'] ?? '';
            return $this->respond([
                'rendered' => glsr(SiteReviewsSummaryShortcode::class)->build($args, 'rest'),
            ]);
        }
        return $this->respond(glsr_get_ratings($args)->toArray());
    }

    public function registerRoutes(): void
    {
        register_rest_route($this->restNamespace(), '/summary/rating', [
            'schema' => [$this, 'schema'],
            [
                'args' => glsr(SummaryParameters::class)->parameters(),
                'callback' => [$this, 'fetchRating'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkSummaryPermission'],
            ],
        ]);
        register_rest_route($this->restNamespace(), '/summary', [
            [
                'args' => glsr(SummaryParameters::class)->parameters(),
                'callback' => [$this, 'fetchSummary'],
                'methods' => \WP_REST_Server::READABLE,
                'permission_callback' => [$this, 'checkSummaryPermission'],
            ],
        ]);
    }

    public function schema(): array
    {
        if (null === $this->schema) {
            $this->schema = glsr(SummarySchema::class)->schema();
        }
        return $this->schema;
    }

    protected function normalizedArgs(\WP_REST_Request $request): array
    {
        return $this->normalizeArgs($request, glsr(SummaryParameters::class)->parameters(), 'summary');
    }
}
