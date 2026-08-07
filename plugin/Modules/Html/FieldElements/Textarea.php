<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;

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
        $field = $this->field;
        $autosize = $field instanceof SettingField && $field->autosize;
        $locations = [
            'setting' => $autosize ? 'autosized large-text' : '',
        ];
        $styles = [
            'setting' => $autosize ? $this->autosizeStyle() : '',
        ];
        return [
            'class' => $locations[$this->field->location()] ?? '',
            'style' => $styles[$this->field->location()] ?? '',
        ];
    }

    protected function autosizeStyle(): string
    {
        // CSS field-sizing ignores the rows attribute. Set the start height from it.
        $rows = max(2, (int) $this->field->rows);
        return "min-height:{$rows}lh";
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
