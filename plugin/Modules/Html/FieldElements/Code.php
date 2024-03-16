<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Code extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'large-text code',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        return [
            'type' => 'textarea',
        ];
    }

    public function tag(): string
    {
        return 'textarea';
    }
}
