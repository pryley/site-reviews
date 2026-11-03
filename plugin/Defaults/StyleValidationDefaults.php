<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StyleValidationDefaults extends DefaultsAbstract
{
    public function __construct()
    {
        $keys = array_keys($this->defaults());
        $this->concatenated = $keys;
        $this->sanitize = array_fill_keys($keys, 'attr-class');
    }

    protected function defaults(): array
    {
        return [
            'field_error' => 'glsr-field-is-invalid',
            'field_hidden' => 'glsr-hidden',
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
