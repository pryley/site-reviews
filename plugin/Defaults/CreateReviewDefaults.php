<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * If this is a form submission, the following fields are overridden:
 * - ip_address
 * - is_approved
 * - is_pinned
 * - is_verified
 */
class CreateReviewDefaults extends Defaults
{
    /**
     * @return array
     */
    public $mapped = [
        '_post_id' => 'post_id',
        '_referer' => 'referer',
        'assign_to' => 'assigned_posts', // support custom assign_to fields
        'category' => 'assigned_terms', // support custom category fields
        'author' => 'name',
    ];

    /**
     * @return array
     */
    public $sanitize = [
        'assigned_posts' => 'post-ids',
        'assigned_terms' => 'term-ids',
        'assigned_users' => 'user-ids',
        'author_id' => 'user-id',
        'avatar' => 'url',
        'content' => 'text-multiline',
        'custom' => 'array',
        'date' => 'date',
        'date_gmt' => 'date',
        'email' => 'user-email',
        'form_id' => 'key',
        'ip_address' => 'text',
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'is_verified' => 'bool',
        'name' => 'user-name',
        'post_id' => 'int',
        'rating' => 'int',
        'referer' => 'text',
        'response' => 'text',
        'terms' => 'bool',
        'terms_exist' => 'bool',
        'title' => 'text',
        'type' => 'text',
        'url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
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
            'terms' => false,
            'terms_exist' => false,
            'title' => '',
            'type' => 'local',
            'url' => '',
        ];
    }

    /**
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if (Cast::toBool(glsr_get($values, 'terms_exist', false))) {
            $values['terms'] = !empty($values['terms']);
        }
        return $values;
    }
}
