<?php

namespace GeminiLabs\SiteReviews\Controllers\Api\Version1\Schema;

class SummarySchema
{
    /**
     * @return array
     */
    public function schema()
    {
        $schema = [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'properties' => $this->properties(),
            'title' => 'rating-summary',
            'type' => 'object',
        ];
        return $schema;
    }

    /**
     * @return array
     */
    protected function properties()
    {
        $properties = [
            'average' => [
                'context' => ['view'],
                'description' => _x('The average rating.', 'admin-text', 'site-reviews'),
                'type' => 'number',
            ],
            'maximum' => [
                'context' => ['view'],
                'description' => _x('The defined maximum rating.', 'admin-text', 'site-reviews'),
                'type' => 'integer',
            ],
            'minimum' => [
                'context' => ['view'],
                'description' => _x('The defined minimum rating.', 'admin-text', 'site-reviews'),
                'type' => 'integer',
            ],
            'ranking' => [
                'context' => ['view'],
                'description' => _x('The bayesian ranking number.', 'admin-text', 'site-reviews'),
                'type' => 'number',
            ],
            'ratings' => [
                'context' => ['view'],
                'description' => _x('The total number of reviews for each rating level from zero to maximum rating.', 'admin-text', 'site-reviews'),
                'items' => ['type' => 'integer'],
                'type' => 'array',
            ],
            'reviews' => [
                'context' => ['view'],
                'description' => _x('The total number of reviews used to calculate the average.', 'admin-text', 'site-reviews'),
                'type' => 'integer',
            ],
        ];
        $properties = glsr()->filterArray('rest-api/summary/schema/properties', $properties);
        ksort($properties);
        return $properties;
    }
}
