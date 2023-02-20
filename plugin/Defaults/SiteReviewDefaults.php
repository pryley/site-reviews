<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Cast;

class SiteReviewDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'debug' => 'bool',
        'hide' => 'array',
        'post_id' => 'int',
    ];

    /**
     * @var string[]
     */
    public $guarded = [
        'fallback',
        'title',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'id' => 'id',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'debug' => false,
            'fallback' => __('Review not found.', 'site-reviews'),
            'hide' => [],
            'id' => '',
            'post_id' => 0,
            'title' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $args = [])
    {
        if (empty($args['post_id'])) {
            $postIds = get_posts([
                'fields' => 'ids',
                'post_status' => 'publish',
                'post_type' => glsr()->post_type,
                'posts_per_page' => 1,
            ]);
            $args['post_id'] = Cast::toInt(array_shift($postIds));
        }
        return $args;
    }
}
