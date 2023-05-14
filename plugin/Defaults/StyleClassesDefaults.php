<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StyleClassesDefaults extends DefaultsAbstract
{
    /**
     * The values that should be concatenated.
     * @var string[]
     */
    public $concatenated = [
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
     * The string that should be used for concatenation.
     * @var string
     */
    protected $glue = ' ';

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
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

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'button' => 'glsr-button',
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
