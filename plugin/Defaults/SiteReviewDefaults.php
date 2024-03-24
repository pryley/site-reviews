<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SiteReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'debug' => 'bool',
        'post_id' => 'int',
    ];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        'fallback', 'title',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'class' => 'attr-class',
        'fallback' => 'text-post',
        'hide' => 'array-string',
        'id' => 'id-hash',
        'title' => 'text',
    ];

    protected function defaults(): array
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
     */
    protected function normalize(array $args = []): array
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
