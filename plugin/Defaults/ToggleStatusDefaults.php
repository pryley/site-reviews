<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ToggleStatusDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'post_id' => 'int',
        'status' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'status' => ['approve', 'pending', 'publish', 'unapprove'],
    ];

    protected function defaults(): array
    {
        return [
            'post_id' => 0,
            'status' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $values['status'] = in_array($values['status'], ['approve', 'publish'])
            ? 'publish'
            : 'pending';
        return $values;
    }
}
