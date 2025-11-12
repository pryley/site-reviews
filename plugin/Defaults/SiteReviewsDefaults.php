<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;

class SiteReviewsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'debug' => 'bool',
        'pagination' => 'string',
        'schema' => 'bool',
        'terms' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'pagination' => ['ajax', 'loadmore', '1', 'true'],
        'terms' => ['0', 'false', '1', 'true'],
    ];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        'fallback',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'assigned_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'className' => 'class',
        'per_page' => 'display',
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'author' => 'user-id',
        'class' => 'attr-class',
        'display' => 'min:1',
        'fallback' => 'text-post',
        'hide' => 'array-string',
        'id' => 'id-unique',
        'offset' => 'min:0',
        'page' => 'min:1',
        'rating' => 'rating',
        'rating_field' => 'name',
        'type' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'assigned_posts' => '',
            'assigned_terms' => '',
            'assigned_users' => '',
            'author' => 0,
            'class' => '',
            'display' => 5,
            'debug' => false,
            'fallback' => '',
            'hide' => [],
            'id' => '',
            'offset' => 0,
            'page' => 1,
            'pagination' => '',
            'rating' => Rating::min(),
            'rating_field' => 'rating', // used for custom rating fields
            'schema' => false,
            'terms' => '',
            'type' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        foreach ($this->mapped as $old => $new) {
            if ('custom' === Arr::get($values, $old)) { // @todo is this deprecated??
                $values[$old] = Arr::get($values, $new);
            }
        }
        return $values;
    }
}
