<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;

class Textarea extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'widget' => 'widefat',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        $locations = [
            'setting' => $this->field->autosize ? 'autosized large-text' : '',
        ];
        return [
            'class' => $locations[$this->field->location()] ?? '',
        ];
    }

    protected function buildSettingField(Arguments $args): string
    {
        $textarea = $this->field->builder()->build($this->tag(), $args->toArray());
        if ($args->autosize && !empty($args->default)) {
            $resetButton = $this->resetButton($args);
            return $this->field->builder()->div([
                'class' => 'has-reset-button',
                'text' => $textarea.$resetButton,
            ]);
        }
        return $textarea;
    }

    protected function resetButton(Arguments $args): string
    {
        return $this->field->builder()->button([
            'aria-label' => esc_attr_x('Reset value to default', 'admin-text', 'site-reviews'),
            'class' => 'button is-reset-button hide-if-no-js',
            'data-default' => esc_attr($args->default),
            'data-tippy-content' => esc_attr_x('Reset value to default', 'admin-text', 'site-reviews'),
            'text' => $this->field->builder()->span([
                'aria-hidden' => 'true',
                'class' => 'dashicons dashicons-undo',
            ]),
            'type' => 'button',
        ]);
    }
}
