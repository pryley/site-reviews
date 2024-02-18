<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults;

use GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults;

class SyncReviewDefaults extends UpdateReviewDefaults
{
    /**
     * The values that should be guarded.
     * @var string[]
     */
    public array $guarded = [
        'assigned_posts',
        'assigned_terms',
        'assigned_users',
        'content',
        'custom',
        'date',
        'date_gmt',
        'ip_address',
        'is_approved',
        'is_pinned',
        'is_verified',
        'response',
        'score',
        'status',
        'title',
        'type',
        'url',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'author' => 'name',
    ];
}
