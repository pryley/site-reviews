<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class FieldConditionDefaults extends Defaults
{
    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'operator' => [
            'contains', 'equals', 'greater', 'less', 'not',
        ],
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'field' => 'name',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'name' => 'attr',
        'operator' => 'attr',
        'value' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'name' => '',
            'operator' => '',
            'value' => '',
        ];
    }
}
