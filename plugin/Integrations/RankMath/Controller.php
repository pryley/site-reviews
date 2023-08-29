<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\RankMath\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Modules\SchemaParser;

class Controller extends BaseController
{
    /**
     * @return array
     * @filter rank_math/schema/validated_data
     */
    public function filterSchema(array $data)
    {
        $this->generateSchema();
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
        if (empty($schemas)) {
            return $data;
        }
        $allowedTypes = glsr(RatingSchemaTypeDefaults::class)->defaults();
        $aggregateRatingSchema = Arr::get($schemas, '0.aggregateRating');
        $reviewSchema = Arr::get($schemas, '0.review');
        foreach ($data as $key => $values) {
            if (!str_starts_with($key, 'schema-')) {
                continue; // Alternatively check key with: \RankMath\Schema\DB::get_schemas((int) get_the_ID());
            }
            if (!in_array(Arr::get($values, '@type'), $allowedTypes)) {
                continue;
            }
            if ($aggregateRatingSchema) {
                $data[$key]['aggregateRating'] = $aggregateRatingSchema;
            }
            if ($reviewSchema) {
                $data[$key]['review'] = $reviewSchema;
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
