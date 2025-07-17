<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsSummaryDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'debug' => 'bool',
        'schema' => 'bool',
        'terms' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'terms' => ['0', 'false', '1', 'true'],
    ];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        'labels', 'text',
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
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'author' => 'user-id:0',
        'class' => 'attr-class',
        'hide' => 'array-string',
        'id' => 'id-unique',
        'labels' => 'text',
        'rating' => 'rating',
        'rating_field' => 'name',
        'text' => 'text-html:a',
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
            'debug' => false,
            'hide' => '',
            'id' => '',
            'labels' => '',
            'rating' => 1,
            'rating_field' => 'rating', // used for custom rating fields
            'schema' => false,
            'terms' => '',
            'text' => '',
            'type' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $values['rating'] = max(1, $values['rating']);
        return $values;
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        foreach ($this->mapped as $old => $new) {
            if ('custom' === Arr::get($values, $old)) {
                $values[$old] = Arr::get($values, $new);
            }
        }
        return parent::normalize($values);
    }
}
