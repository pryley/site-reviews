<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SiteReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'debug' => 'bool',
        'post_id' => 'int',
    ];

    /**
     * The values that should be guarded.
     * @var string[]
     */
    public $guarded = [
        'fallback', 'title',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'class' => 'attr-class',
        'fallback' => 'text-post',
        'hide' => 'array-string',
        'id' => 'id-hash',
        'title' => 'text',
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
