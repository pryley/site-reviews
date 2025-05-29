<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class SectionDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'children' => 'array-consolidate',
        'fields' => 'array-string',
        'label' => 'text',
        'options' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'children' => [],
            'fields' => [],
            'label' => '',
            'options' => [],
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        if (empty($values['children'])) {
            return $values;
        }
        $children = array_filter($values['children'], function ($child) {
            $type = $child['options']['type'] ?? 'alert_box';
            if ('alert_box' !== $type) {
                return true;
            }
            return !empty($child['options']['alertBoxOptions']['content']);
        });
        $values['children'] = array_values($children);
        return $values;
    }
}
