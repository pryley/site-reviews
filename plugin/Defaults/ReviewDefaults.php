<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'author_id' => 'int',
        'is_approved' => 'bool',
        'is_modified' => 'bool',
        'is_pinned' => 'bool',
        'rating' => 'int',
        'rating_id' => 'int',
    ];

    /**
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
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'array-int',
        'assigned_terms' => 'array-int',
        'assigned_users' => 'array-int',
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
            'rating' => '',
            'rating_id' => '',
            'response' => '',
            'status' => '',
            'title' => '',
            'type' => '',
            'url' => '',
        ];
    }
}
