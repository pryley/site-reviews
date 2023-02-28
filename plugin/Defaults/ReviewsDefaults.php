<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;

class ReviewsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'ip_address' => 'string',
        'order' => 'name',
        'orderby' => 'name',
        'pagination' => 'string',
        'status' => 'name',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     * @var array
     */
    public $enums = [
        'order' => ['asc', 'desc'],
        'orderby' => [
            'author',
            'comment_count',
            'date',
            'date_gmt',
            'id',
            'menu_order',
            'none',
            'random',
            'rating',
        ],
        'status' => ['all', 'approved', 'pending', 'publish', 'unapproved'],
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     * @var array
     */
    public $mapped = [
        'assigned_to' => 'assigned_posts',
        'author_id' => 'user__in',
        'category' => 'assigned_terms',
        'count' => 'per_page', // @deprecated in v4.1.0
        'display' => 'per_page',
        'exclude' => 'post__not_in',
        'include' => 'post__in',
        'user' => 'assigned_users',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_posts_types' => 'array-string',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'content' => 'text-multiline',
        'email' => 'email',
        'ip_address' => 'text',
        'offset' => 'min:0',
        'page' => 'min:1',
        'per_page' => 'min:1',
        'post__in' => 'array-int',
        'post__not_in' => 'array-int',
        'rating' => 'rating',
        'rating_field' => 'name',
        'type' => 'slug',
        'user__in' => 'user-ids',
        'user__not_in' => 'user-ids',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_posts' => '',
            'assigned_posts_types' => [],
            'assigned_terms' => '',
            'assigned_users' => '',
            'content' => '',
            'date' => '', // can be an array or string
            'email' => '',
            'ip_address' => '',
            'offset' => 0,
            'order' => 'desc',
            'orderby' => 'date',
            'page' => 1,
            'pagination' => false,
            'per_page' => 10,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'rating_field' => 'rating', // used for custom rating fields
            'status' => 'approved',
            'terms' => '',
            'type' => '',
            'user__in' => [],
            'user__not_in' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function normalize(array $values = [])
    {
        if (empty($values['assigned_posts'])) {
            return $values;
        }
        $postIds = Cast::toArray($values['assigned_posts']);
        $values['assigned_posts_types'] = [];
        foreach ($postIds as $postType) {
            if (!is_numeric($postType) && post_type_exists($postType)) {
                $values['assigned_posts'] = []; // query only by assigned post types!
                $values['assigned_posts_types'][] = $postType;
            }
        }
        return $values;
    }
}
