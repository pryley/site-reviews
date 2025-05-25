<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;
use GeminiLabs\SiteReviews\Helper;

class ControlDefaults extends DefaultsAbstract
{
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
    ];

    protected function defaults(): array
    {
        return [
            'description' => '',
            'label' => '',
            'type' => 'text',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $types = [
            'number' => 'slider',
            'text' => 'textfield',
        ];
        if (array_key_exists($values['type'], $types)) {
            $values['type'] = $types[$values['type']];
        }
        $values['full_width'] ??= true;
        $method = Helper::buildMethodName('finalize', $values['type']);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $values);
        }
        return $values;
    }

    protected function finalizeCheckbox(array $values = []): array
    {
        if (!empty($values['options'])) {
            $values['type'] = 'select';
            $values['config'] = [
                'multiple' => true,
                'options' => $values['options'],
                'placeholder' => $values['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews'),
                'sortable' => false,
            ];
            unset($values['options']);
        } elseif (!isset($values['options'])) {
            $values['type'] = 'radio-buttons';
            $values['options'] = [
                '' => ['title' => _x('No', 'admin-text', 'site-reviews')],
                'true' => ['title' => _x('Yes', 'admin-text', 'site-reviews')],
            ];
        }
        return $values;
    }

    protected function finalizeSelect(array $values = []): array
    {
        $values['config'] = [
            'sortable' => false,
        ];
        if (!empty($values['options'])) {
            if (!isset($values['options'][''])) {
                $placeholder = $values['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
                $values['options'] = ['' => $placeholder] + $values['options'];
            }
        } elseif (!isset($values['options'])) {
            if ('assigned_terms' === $values['name']) {
                $values['config']['termSelect'] = [
                    'taxonomies' => glsr()->taxonomy,
                ];
            } else {
                $values['config']['postSelect'] = glsr()->prefix.$values['name'];
            }
            if (str_starts_with($values['name'], 'assigned_')) {
                $values['multiple'] = true;
            }
            $values['config']['multiple'] = $values['multiple'] ?? false;
            $values['config']['placeholder'] = $values['placeholder'] ?? esc_html_x('Select...', 'admin-text', 'site-reviews');
            unset($values['multiple']);
            unset($values['placeholder']);
        }
        return $values;
    }
}
