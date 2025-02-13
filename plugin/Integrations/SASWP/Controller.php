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
        $aggregateRating = Arr::get($schema, 'aggregateRating');
        $review = Arr::get($schema, 'review');
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
