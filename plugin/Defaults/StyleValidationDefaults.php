<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StyleValidationDefaults extends DefaultsAbstract
{
    /**
     * The values that should be concatenated.
     * @var string[]
     */
    public $concatenated = [
        'field_error',
        'field_message',
        'field_required',
        'field_valid',
        'form_error',
        'form_message',
        'form_message_failed',
        'form_message_success',
        'input_error',
        'input_valid',
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
        'field_error' => 'attr-class',
        'field_message' => 'attr-class',
        'field_required' => 'attr-class',
        'field_valid' => 'attr-class',
        'form_error' => 'attr-class',
        'form_message' => 'attr-class',
        'form_message_failed' => 'attr-class',
        'form_message_success' => 'attr-class',
        'input_error' => 'attr-class',
        'input_valid' => 'attr-class',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'field_error' => 'glsr-field-is-invalid',
            'field_message' => 'glsr-field-error',
            'field_required' => 'glsr-required',
            'field_valid' => 'glsr-field-is-valid',
            'form_error' => 'glsr-form-is-invalid',
            'form_message' => 'glsr-form-message',
            'form_message_failed' => 'glsr-form-failed',
            'form_message_success' => 'glsr-form-success',
            'input_error' => 'glsr-is-invalid',
            'input_valid' => 'glsr-is-valid',
        ];
    }
}
