<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Modules\Html\SettingField;

class Color extends AbstractFieldElement
{
    public function buildSettingField(Arguments $args): string
    {
        $field = $this->field;
        if (empty($args->value) && $field instanceof SettingField) {
            $args->value = $field->default; // fallback to the default value
        }
        return $this->field->builder()->build($this->tag(), $args->toArray());
    }

    public function required(): array
    {
        $locations = [
            'setting' => [
                'class' => 'glsr-color-picker color-picker-hex',
                'type' => 'text',
            ],
        ];
        return $locations[$this->field->location()] ?? [];
    }

    public function tag(): string
    {
        return 'input';
    }
}
