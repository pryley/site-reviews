<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * If this is a form submission, the following fields are overridden:
 * - author_id
 * - ip_address
 * - is_approved
 * - is_pinned
 * - is_verified
 * - response
 * - response_by.
 */
class CreateReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'is_verified' => 'bool',
        'post_id' => 'int',
        'terms' => 'bool',
        'terms_exist' => 'bool',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        '_post_id' => 'post_id',
        '_referer' => 'referer',
        'assign_to' => 'assigned_posts', // support custom assign_to fields
        'category' => 'assigned_terms', // support custom category fields
        'author' => 'name',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'author_id' => 'user-id',
        'avatar' => 'url',
        'content' => 'text-multiline',
        'custom' => 'array-consolidate',
        'date' => 'date',
        'date_gmt' => 'date',
        'email' => 'user-email',
        'form_id' => 'id',
        'ip_address' => 'ip-address',
        'name' => 'user-name:current_user',
        'rating' => 'rating',
        'referer' => 'text',
        'response' => 'text-html',
        'response_by' => 'user-id:0',
        'title' => 'text',
        'type' => 'slug',
        'url' => 'url',
    ];

    protected function defaults(): array
    {
        return [
            'assigned_posts' => [],
            'assigned_terms' => [],
            'assigned_users' => [],
            'author_id' => 0,
            'avatar' => '',
            'content' => '',
            'custom' => [],
            'date' => '',
            'date_gmt' => '',
            'email' => '',
            'form_id' => '',
            'ip_address' => '',
            'is_approved' => true,
            'is_pinned' => false,
            'is_verified' => false,
            'name' => '',
            'post_id' => '',
            'rating' => '',
            'referer' => '',
            'response' => '',
            'response_by' => 0,
            'terms' => false,
            'terms_exist' => false,
            'title' => '',
            'type' => 'local',
            'url' => '',
        ];
    }

    protected function normalize(array $values = []): array
    {
        if (Arr::getAs('bool', $values, 'terms_exist', false)) {
            $values['terms'] = !empty($values['terms']);
        }
        return $values;
    }
}
