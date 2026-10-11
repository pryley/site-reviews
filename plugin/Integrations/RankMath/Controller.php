<?php

namespace GeminiLabs\SiteReviews\Integrations\RankMath;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Defaults\RatingSchemaTypeDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
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
        foreach ($data as $key => $values) {
            if ('richSnippet' !== $key
                && !str_starts_with($key, 'new-') // used in preview
                && !str_starts_with($key, 'schema-')) {
                continue;
            }
            if (!in_array($values['@type'] ?? '', $allowedTypes)) {
                continue;
            }
            $data[$key] = glsr(Schema::class)->rate($values, $schema);
        }
        return $data;
    }
}
