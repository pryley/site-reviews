<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Number extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'small-text',
            'widget' => 'small-text',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        return [
            'validation' => 'number',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }
}
