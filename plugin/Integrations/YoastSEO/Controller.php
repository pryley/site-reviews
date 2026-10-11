<?php

namespace GeminiLabs\SiteReviews\Integrations\YoastSEO;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    /**
     * @param array $graph
     *
     * @filter wpseo_schema_graph
     */
    public function filterSchema($graph): array
    {
        $graph = Arr::consolidate($graph);
        if (function_exists('is_product') && is_product()) {
            return $graph; // skip WooCommerce products
        }
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $graph;
        }
        $key = (new Graph($graph))->ratedKey();
        if (null === $key) {
            return $graph;
        }
        $graph[$key] = glsr(Schema::class)->rate(Arr::consolidate($graph[$key]), $schema);
        return $graph;
    }
}
