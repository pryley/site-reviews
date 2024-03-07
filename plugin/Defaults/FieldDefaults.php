<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FieldDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'after' => 'string',
        'description' => 'string',
        'id' => 'string',
        'label' => 'string',
        'multiple' => 'bool',
        'name' => 'string',
        'required' => 'bool',
        'text' => 'string',
        'type' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'class' => 'attr-class',
        'options' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'after' => '',
            'class' => '',
            'description' => '',
            'id' => '',
            'label' => '',  // this value is likely a generated HTML string
            'multiple' => false,
            'name' => '',
            'options' => [],
            'required' => false,
            'text' => '', // this value is likely a generated HTML string
            'type' => '',
            'value' => '', // this value can also be an array
        ];
    }
}
