<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\SchemaParser;

class Controller extends BaseController
{
    /**
     * @param array $data
     * @filter saswp_modify_reviews_schema
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        $this->generateSchema();
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
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

    protected function generateSchema(): void
    {
        if (empty(glsr()->retrieve('schemas', []))) {
            glsr(Schema::class)->store(
                glsr(SchemaParser::class)->generate()
            );
        }
    }
}
