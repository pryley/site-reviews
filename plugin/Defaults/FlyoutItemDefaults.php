<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FlyoutItemDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'icon' => 'attr-class',
        'title' => 'text',
        'url' => 'url',
    ];

    protected function defaults(): array
    {
        return [
            'icon' => '',
            'title' => '',
            'url' => '',
        ];
    }
}
