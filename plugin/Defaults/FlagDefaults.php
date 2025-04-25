<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FlagDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'data-border' => 'bool',
        'data-radius' => 'bool',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'data-gradient' => [
            '', 'circular', 'diagonal', 'linear',
        ],
        'data-shape' => [
            '', 'circle', 'rectangle', 'square',
        ],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'data-gradient' => 'attr',
        'data-shape' => 'attr',
    ];

    protected function defaults(): array
    {
        return [
            'data-border' => true,
            'data-gradient' => 'linear',
            'data-radius' => true,
            'data-shape' => '',
        ];
    }
}
