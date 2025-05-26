<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder\Defaults;

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
        'label' => 'heading',
        'name' => 'param_name',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'description' => 'text', // Flatsome does not support HTML in descriptions
    ];

    protected function defaults(): array
    {
        return [
            'group' => 'general',
            'label' => '',
            'type' => 'textfield',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $types = [
            'text' => 'textfield',
        ];
        if (array_key_exists($values['type'], $types)) {
            $values['type'] = $types[$values['type']];
        }
        return $values;
    }
}
