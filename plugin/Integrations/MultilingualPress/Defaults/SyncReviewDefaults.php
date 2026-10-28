<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults;

use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
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

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        // A translation keeps its own custom field values so every custom field key is guarded.
        // The copier flattens them to the top level.
        $customKeys = array_keys(glsr(CustomFieldsDefaults::class)->call('merge', $values));
        $this->guarded = array_values(array_unique(array_merge($this->guarded, $customKeys)));
        return parent::normalize($values);
    }
}
