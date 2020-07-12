<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ReviewsDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'offset' => 'int',
        'page' => 'int',
        'pagination' => 'string',
        'per_page' => 'int',
        'rating' => 'int',
    ];

    /**
     * @var array
     */
    public $mapped = [
        'assigned_to' => 'assigned_posts',
        'category' => 'assigned_terms',
        'count' => 'per_page', // @deprecated in v4.1.0
        'display' => 'per_page',
        'user' => 'assigned_users',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'post__in' => 'array-int',
        'post__not_in' => 'array-int',
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
            'offset' => '',
            'order' => 'DESC',
            'orderby' => 'date',
            'page' => 1,
            'pagination' => false,
            'per_page' => 10,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'type' => '',
        ];
    }
}
