<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Text extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'regular-text',
            'widget' => 'widefat',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function tag(): string
    {
        return 'input';
    }
}
