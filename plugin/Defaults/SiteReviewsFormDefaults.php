<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class SiteReviewsFormDefaults extends Defaults
{
    /**
     * The values that should be guarded.
     * @var string[]
     */
    public $guarded = [
        'description', 'title',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * @var array
     */
    public $mapped = [
        'assign_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and $enums
     * @var array
     */
    public $sanitize = [
        'class' => 'attr-class',
        'debug' => 'bool',
        'description' => 'text',
        'form_id' => 'id',
        'hide' => 'array-string',
        'id' => 'id',
        'reviews_id' => 'id',
        'title' => 'text',
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
            'description' => '',
            'form_id' => '', // used for the validation session key and to generate the honeypot hash
            'hide' => '',
            'id' => '',
            'reviews_id' => '',
            'title' => '',
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
