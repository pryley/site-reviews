<?php

namespace GeminiLabs\SiteReviews\Defaults;

class DashboardDataDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'value' => 'int',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'dashicon' => 'attr-class',
        'label' => 'text',
        'url' => 'url',
    ];

    protected function defaults(): array
    {
        return [
            'dashicon' => '',
            'label' => '',
            'url' => '',
            'value' => 0,
        ];
    }
}
