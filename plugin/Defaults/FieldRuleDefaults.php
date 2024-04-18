<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Contracts\PluginContract;
use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Str;

class FieldRuleDefaults extends Defaults
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'parameters' => 'array',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'rule' => [
            'accepted', 'between', 'email', 'max', 'min', 'number', 'regex', 
            'required', 'tel', 'url',
        ],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'rule' => 'attr',
    ];

    protected function defaults(): array
    {
        return [
            'parameters' => [],
            'rule' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $parameters = $values['parameters'] ?? [];
        $parameters = array_filter($parameters, fn ($val) => !Helper::isEmpty($val));
        $parameters = array_map(fn ($val) => is_numeric($val) ? intval($val) : $val, $parameters);
        $values['parameters'] = $parameters;
        return $values;
    }
}
