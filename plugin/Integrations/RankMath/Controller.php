<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\RankMath\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Modules\Schema;

class Controller extends AbstractController
{
    /**
     * @filter rank_math/schema/validated_data
     */
    public function filterSchema($data): array
    {
        $data = Arr::consolidate($data);
        $schema = glsr(Schema::class)->generate();
        if (empty($schema)) {
            return $data;
        }
        $allowedTypes = glsr(RatingSchemaTypeDefaults::class)->defaults();
        $aggregateRatingSchema = Arr::get($schema, 'aggregateRating');
        $reviewSchema = Arr::get($schema, 'review');
        foreach ($data as $key => $values) {
            $type = $values['@type'] ?? '';
            if (!in_array($type, $allowedTypes)) {
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
}
