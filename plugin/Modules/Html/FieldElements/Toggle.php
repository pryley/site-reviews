<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Toggle extends Checkbox
{
    public function required(): array
    {
        return [
            'type' => 'checkbox',
        ];
    }
}
