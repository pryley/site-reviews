<?php

namespace GeminiLabs\SiteReviews\Integrations\YoastSEO;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Defaults\RatingSchemaTypeDefaults;
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
        $allowedTypes = glsr(RatingSchemaTypeDefaults::class)->defaults();
        $aggregateRatingSchema = Arr::get($schema, 'aggregateRating');
        $reviewSchema = Arr::get($schema, 'review');
        foreach ($graph as $key => $item) {
            $types = Arr::getAs('array', $item, '@type');
            if (empty(array_intersect($types, $allowedTypes))) {
                continue;
            }
            $isReviewType = !empty(array_intersect($types, ['Review', 'ReviewNewsArticle']));
            if (!empty($aggregateRatingSchema)) {
                if ($isReviewType) {
                    $graph[$key]['itemReviewed']['aggregateRating'] = $aggregateRatingSchema;
                } else {
                    $graph[$key]['aggregateRating'] = $aggregateRatingSchema;
                }
            }
            if (!empty($reviewSchema)) {
                if ($isReviewType) {
                    $graph[$key]['itemReviewed']['review'] = $reviewSchema;
                } else {
                    $graph[$key]['review'] = $reviewSchema;
                }
            }
        }
        return $graph;
    }
}
