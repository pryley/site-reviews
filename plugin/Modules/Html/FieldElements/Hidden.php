<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Hidden extends AbstractFieldElement
{
    public function required(): array
    {
        return [
            'is_raw' => true,
        ];
    }

    public function tag(): string
    {
        return 'input';
    }
}
