<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Url extends Text
{
    public function required(): array
    {
        return [
            'validation' => 'url',
        ];
    }
}
