<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Text extends AbstractFieldElement
{
    public function defaults(): array
    {
        $settingInputSizeClass = 1 !== preg_match('/(tiny|small|regular|large)-text/', $this->field->class)
            ? 'regular-text'
            : '';
        $locations = [
            'setting' => $settingInputSizeClass,
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
