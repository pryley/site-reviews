<?php

namespace GeminiLabs\SiteReviews\Integrations\SASWP;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;

class Controller extends BaseController
{
    /**
     * @filter saswp_modify_schema_output
     */
    public function filterSchema(array $schema): array
    {
        $schemas = glsr()->filterArray('schema/all', glsr()->retrieve('schemas', []));
        if (empty($schemas)) {
            return $schema;
        }
        $types = Arr::consolidate(glsr_get_option('schema.integration.types'));
        foreach ($schema as $key => $values) {
            $type = Arr::get($values, '@type');
            if (!in_array($type, $types)) {
                continue;
            }
            if ($rating = Arr::get($schemas, '0.aggregateRating')) {
                $schema[$key]['aggregateRating'] = $rating;
            }
            if ($review = Arr::get($schemas, '0.review')) {
                $schema[$key]['review'] = $review;
            }
        }
        return $schema;
    }

    /**
     * @filter site-reviews/settings/sanitize
     */
    public function filterSettingsSanitize(array $options, array $input): array
    {
        $key = 'settings.schema.integration.types';
        $options = Arr::set($options, $key, Arr::get($input, $key, []));
        return $options;
    }
}
