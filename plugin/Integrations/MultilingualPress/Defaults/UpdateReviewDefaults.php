<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults;

use GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults as Defaults;

class UpdateReviewDefaults extends Defaults
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $guarded = [
        'assigned_posts',
        'assigned_terms',
        'assigned_users',
        'author_id',
        'content',
        'date',
        'date_gmt',
        'edit_url',
        'ID',
        'ip_address',
        'is_approved',
        'is_modified',
        'is_pinned',
        'is_verified',
        'rating_id',
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
