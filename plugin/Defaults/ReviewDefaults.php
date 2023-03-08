<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class ReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'author_id' => 'int',
        'is_approved' => 'bool',
        'is_modified' => 'bool',
        'is_pinned' => 'bool',
        'is_verified' => 'bool',
        'rating' => 'int',
        'rating_id' => 'int',
        'terms' => 'bool',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     * @var array
     */
    public $mapped = [
        'ID' => 'rating_id',
        'name' => 'author',
        'post_ids' => 'assigned_posts',
        'term_ids' => 'assigned_terms',
        'user_ids' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'array-int',
        'assigned_terms' => 'array-int',
        'assigned_users' => 'array-int',
        'author' => 'text',
        'avatar' => 'url',
        'content' => 'text-multiline',
        'date' => 'date',
        'date_gmt' => 'date',
        'email' => 'email',
        'ip_address' => 'text',
        'response' => 'text-multiline',
        'score' => 'min:0',
        'status' => 'text',
        'title' => 'text',
        'type' => 'slug',
        'url' => 'url',
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
            'author' => '',
            'author_id' => '',
            'avatar' => '',
            'content' => '',
            'custom' => '',
            'date' => '',
            'date_gmt' => '',
            'email' => '',
            'ID' => '',
            'ip_address' => '',
            'is_approved' => false,
            'is_modified' => false,
            'is_pinned' => false,
            'is_verified' => false,
            'rating' => '',
            'rating_id' => '',
            'response' => '',
            'score' => '',
            'status' => '',
            'terms' => true,
            'title' => '',
            'type' => '',
            'url' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $date = Arr::get($values, 'date');
        if ($date && '0000-00-00 00:00:00' === Arr::get($values, 'date_gmt')) {
            $values['date_gmt'] = get_gmt_from_date($date);
        }
        return $values;
    }
}
