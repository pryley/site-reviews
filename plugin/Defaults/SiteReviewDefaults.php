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
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'className' => 'class',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'class' => 'attr-class',
        'hide' => 'array-string',
        'id' => 'id-unique',
    ];

    protected function defaults(): array
    {
        return [
            'class' => '',
            'debug' => false,
            'hide' => [],
            'id' => '',
            'post_id' => 0,
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
