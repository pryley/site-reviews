<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ControlDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'full_width' => 'bool',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'label' => 'heading',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'description' => 'text', // Flatsome does not support HTML in descriptions
        'group' => 'text', // Support passing a title instead of a slug
        'type' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'description' => '',
            'full_width' => true,
            'group' => 'general',
            'label' => '',
            'type' => 'text',
        ];
    }
}
