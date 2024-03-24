<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

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
}
