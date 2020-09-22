<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class StyleValidationDefaults extends Defaults
{
    /**
     * The keys that should be concatenated, value is the glue.
     * @var array
     */
    public $concatenated = [
        'error_tag_class' => ' ',
        'field_class' => ' ',
        'field_error_class' => ' ',
        'input_error_class' => ' ',
        'input_valid_class' => ' ',
        'message_error_class' => ' ',
        'message_initial_class' => ' ',
        'message_success_class' => ' ',
        'message_tag_class' => ' ',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'error_tag' => 'div',
            'error_tag_class' => 'glsr-field-error',
            'field_class' => 'glsr-field',
            'field_error_class' => 'glsr-has-error',
            'input_error_class' => 'glsr-is-invalid',
            'input_valid_class' => 'glsr-is-valid',
            'message_error_class' => 'glsr-has-errors',
            'message_initial_class' => 'glsr-is-visible',
            'message_success_class' => 'glsr-has-success',
            'message_tag' => 'div',
            'message_tag_class' => 'glsr-form-message',
        ];
    }
}
