<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Tel extends Text
{
    public function required(): array
    {
        return [
            'validation' => 'tel',
        ];
    }
}
