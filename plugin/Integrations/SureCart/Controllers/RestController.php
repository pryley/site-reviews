<?php

namespace GeminiLabs\SiteReviews\Integrations\SureCart\Controllers;

use GeminiLabs\SiteReviews\Contracts\ControllerContract;
use GeminiLabs\SiteReviews\HookProxy;
use SureCart\Models\Model;

class RestController implements ControllerContract
{
    use HookProxy;

    /**
     * @filter site-reviews/rest-api/summary/args
     */
    public function filterRestApiSummaryArgs(array $args, \WP_REST_Request $request): array
    {
        if ('/site-reviews/v1/summary/rating' !== $request->get_route()) {
            return $args;
        }
        if (!str_contains((string) $request->get_param('_block'), 'surecart-product-rating')) {
            return $args;
        }
        $args['theme'] = glsr_get_option('integrations.surecart.style');
        return $args;
    }
}
