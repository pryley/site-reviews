<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Color extends AbstractFieldElement
{
    public function buildSettingField(array $args = []): string
    {
        if (empty($args['value'])) {
            $args['value'] = $this->field->default; // fallback to the default value
        }
        return $this->field->builder()->build($this->tag(), $args);
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
