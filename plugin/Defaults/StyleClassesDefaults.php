<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StyleClassesDefaults extends DefaultsAbstract
{
    /**
     * Return the base classes, a style's classes are added to them.
     */
    protected function defaults(): array
    {
        return [
            'button' => 'glsr-button',
            'description' => 'glsr-description',
            'field' => 'glsr-field',
            'fieldset' => '',
            'form' => 'glsr-form',
            'input' => '',
            'input_checkbox' => '',
            'input_radio' => '',
            'label' => 'glsr-label',
            'select' => '',
            'textarea' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        if ('glsr-button' === ($values['button'] ?? '')) {
            $values['button'] .= ' wp-block-button__link wp-element-button'; // fallback to block styling!
        }
        return $values;
    }

    /**
     * Concatenate and sanitize every slot, including one a config adds for a field type.
     */
    protected function normalize(array $values = []): array
    {
        $keys = array_keys($this->defaults + $values);
        $this->concatenated = $keys;
        $this->sanitize = array_fill_keys($keys, 'attr-class');
        return $values;
    }
}
