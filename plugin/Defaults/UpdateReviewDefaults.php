<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * This is only used when updating the Review Post.
 */
class UpdateReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'status' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'status' => ['pending', 'publish'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'content' => 'text-multiline',
        'date' => 'date',
        'date_gmt' => 'date',
        'title' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'content' => '',
            'date' => '',
            'date_gmt' => '',
            'status' => '',
            'title' => '',
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        if (isset($values['is_approved'])) {
            $values['status'] = wp_validate_boolean($values['is_approved']) ? 'publish' : 'pending';
        } else {
            $values['status'] = '';
        }
        return $values;
    }
}
