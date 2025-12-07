<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ControlDefaults extends DefaultsAbstract
{
    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'group' => [
            'advanced', 'design', 'general',
        ],
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'default' => 'std',
        'label' => 'heading',
        'name' => 'param_name',
    ];

    protected function defaults(): array
    {
        return [
            'group' => 'general',
            'heading' => '',
            'param_name' => '',
            'std' => '',
            'type' => 'textfield',
        ];
    }
}
