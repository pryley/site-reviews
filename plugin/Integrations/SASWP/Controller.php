<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    /**
     * @param array $data
     *
     * @filter saswp_modify_reviews_schema
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $data;
        }
        $aggregateRatingSchema = Arr::get($schema, 'aggregateRating');
        $reviewSchema = Arr::get($schema, 'review');
        $isReviewType = in_array(Arr::get($data, '@type'), ['Review', 'ReviewNewsArticle']);
        if (!empty($aggregateRatingSchema)) {
            if ($isReviewType) {
                $data['itemReviewed']['aggregateRating'] = $aggregateRatingSchema;
            } else {
                $data['aggregateRating'] = $aggregateRatingSchema;
            }
        }
        if (!empty($reviewSchema)) {
            if ($isReviewType) {
                $data['itemReviewed']['review'] = $reviewSchema;
            } else {
                $data['review'] = $reviewSchema;
            }
        }
        return $data;
    }
}
