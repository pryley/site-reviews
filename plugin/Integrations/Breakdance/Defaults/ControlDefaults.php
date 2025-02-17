<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ControlDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'enableHover' => 'bool',
        'enableMediaQueries' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'children' => 'array-consolidate',
        'keywords' => 'array-consolidate',
        'label' => 'text',
        'options' => 'array-consolidate',
        'slug' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'children' => [],
            'enableHover' => false,
            'enableMediaQueries' => false,
            'keywords' => [],
            'label' => '',
            'options' => [],
            'slug' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        if (empty($values['label'])) {
            $values['label'] = ucwords(str_replace(['-', '_'], ' ', $values['slug']));
        }
        return $values;
    }
}
