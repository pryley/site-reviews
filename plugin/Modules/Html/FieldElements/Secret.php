<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;

class Secret extends Text
{
    public function required(): array
    {
        return [
            'autocomplete' => 'off',
            'spellcheck' => 'false',
            'type' => 'text',
        ];
    }

    protected function buildSettingField(Arguments $args): string
    {
        $input = $this->field->builder()->build($this->tag(), $args->toArray());
        if (empty($this->field->value)) {
            return $input;
        }
        $button = $this->field->builder()->button([
            'aria-label' => esc_attr_x('Show value', 'admin-text', 'site-reviews'),
            'class' => 'button wp-hide-pw hide-if-no-js',
            'data-hide' => esc_attr_x('Hide value', 'admin-text', 'site-reviews'),
            'data-show' => esc_attr_x('Show value', 'admin-text', 'site-reviews'),
            'text' => $this->field->builder()->span([
                'aria-hidden' => 'true',
                'class' => 'dashicons dashicons-visibility',
            ]),
            'type' => 'button',
        ]);
        return $this->field->builder()->span([
            'class' => 'wp-pwd',
            'text' => $input.$button,
        ]);
    }
}
