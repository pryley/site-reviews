<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Radio extends Checkbox
{
    public function required(): array
    {
        return [
            'class' => 'glsr-input-radio',
            'type' => 'radio',
        ];
    }
}
