<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FeatureDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'premium' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'feature' => 'text',
        'tooltip' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'feature' => '',
            'premium' => false,
            'tooltip' => '',
        ];
    }
}
