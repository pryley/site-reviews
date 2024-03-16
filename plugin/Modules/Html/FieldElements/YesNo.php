<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class YesNo extends AbstractFieldElement
{
    public function defaults(): array
    {
        $locations = [
            'setting' => 'inline',
            'widget' => 'inline',
        ];
        return array_filter([
            'class' => $locations[$this->field->location()] ?? '',
        ]);
    }

    public function required(): array
    {
        return [
            'options' => [
                'no' => __('No', 'site-reviews'),
                'yes' => __('Yes', 'site-reviews'),
            ],
            'type' => 'radio',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }
}
