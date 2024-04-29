<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StyleClassesDefaults extends DefaultsAbstract
{
    /**
     * The values that should be concatenated.
     *
     * @var string[]
     */
    public array $concatenated = [
        'button',
        'description',
        'field',
        'form',
        'input',
        'input_checkbox',
        'input_radio',
        'label',
        'label_checkbox',
        'label_radio',
        'select',
        'textarea',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'button' => 'attr-class',
        'description' => 'attr-class',
        'field' => 'attr-class',
        'form' => 'attr-class',
        'input' => 'attr-class',
        'input_checkbox' => 'attr-class',
        'input_radio' => 'attr-class',
        'label' => 'attr-class',
        'label_checkbox' => 'attr-class',
        'label_radio' => 'attr-class',
        'select' => 'attr-class',
        'textarea' => 'attr-class',
    ];

    protected function defaults(): array
    {
        return [
            'button' => 'glsr-button wp-block-button__link',
            'description' => 'glsr-description',
            'field' => 'glsr-field',
            'form' => 'glsr-form',
            'input' => '',
            'input_checkbox' => '',
            'input_radio' => '',
            'label' => 'glsr-label',
            'label_checkbox' => '',
            'label_radio' => '',
            'select' => '',
            'textarea' => '',
        ];
    }
}
