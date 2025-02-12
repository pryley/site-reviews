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
        $schemas = glsr(Schema::class)->generate();
        if (empty($schemas)) {
            return $data;
        }
        $aggregateRating = Arr::get($schemas, '0.aggregateRating');
        $review = Arr::get($schemas, '0.review');
        if (in_array(Arr::get($data, '@type'), ['Review', 'ReviewNewsArticle'])) {
            if (!empty($aggregateRating)) {
                $data['itemReviewed']['aggregateRating'] = $aggregateRating;
            }
            if (!empty($review)) {
                $data['itemReviewed']['review'] = $review;
            }
        } else {
            if (!empty($aggregateRating)) {
                $data['aggregateRating'] = $aggregateRating;
            }
            if (!empty($review)) {
                $data['review'] = $review;
            }
        }
        return $data;
    }
}
