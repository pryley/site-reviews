<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsSummaryDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'debug' => 'bool',
        'schema' => 'bool',
        'terms' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     * @var array
     */
    public $enums = [
        'terms' => ['0', 'false', '1', 'true'],
    ];

    /**
     * The values that should be guarded.
     * @var string[]
     */
    public $guarded = [
        'labels', 'text', 'title',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     * @var array
     */
    public $mapped = [
        'assigned_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'class' => 'attr-class',
        'hide' => 'array-string',
        'id' => 'id-hash',
        'labels' => 'text',
        'rating' => 'rating',
        'rating_field' => 'name',
        'text' => 'text',
        'title' => 'text',
        'type' => 'slug',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'class' => '',
            'debug' => false,
            'hide' => '',
            'id' => '',
            'labels' => '',
            'rating' => 1,
            'rating_field' => 'rating', // used for custom rating fields
            'schema' => false,
            'terms' => '',
            'text' => '',
            'title' => '',
            'type' => 'local',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        foreach ($this->mapped as $old => $new) {
            if ('custom' === Arr::get($values, $old)) {
                $values[$old] = Arr::get($values, $new);
            }
        }
        return parent::normalize($values);
    }
}
