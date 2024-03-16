<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FieldDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'checked' => 'bool',
        'multiple' => 'bool',
        'required' => 'bool',
        'selected' => 'bool',
        'text' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'after' => 'text',
        'class' => 'attr-class',
        'description' => 'text-html:a,br,code,span',
        'id' => 'attr',
        'label' => 'text-html:a,code',
        'name' => 'attr',
        'options' => 'array-consolidate',
        'type' => 'attr',
    ];

    protected function defaults(): array
    {
        return [
            'after' => '',
            'checked' => false,
            'class' => '',
            'description' => '',
            'id' => '',
            'label' => '',
            'multiple' => false,
            'name' => '',
            'options' => [],
            'required' => false,
            'selected' => false,
            'text' => '', // this value could be a HTML string
            'type' => '',
            'value' => '', // this value could be either an array or string
        ];
    }
}
