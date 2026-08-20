<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Parameters;

trait NormalizesArgs
{
    /**
     * Reduces a request to the values of its registered parameters.
     */
    protected function normalizeArgs(\WP_REST_Request $request, array $registered, string $restBase): array
    {
        $args = [];
        foreach ($registered as $key => $params) {
            if (isset($request[$key])) {
                $args[$key] = $request[$key];
            }
        }
        if (empty($args['date'])) {
            $args['date'] = [
                'after' => $args['after'] ?? '',
                'before' => $args['before'] ?? '',
            ];
        }
        return glsr()->filterArray("rest-api/{$restBase}/args", $args, $request);
    }
}
