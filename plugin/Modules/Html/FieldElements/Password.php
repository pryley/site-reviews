<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

class Password extends Text
{
    public function required(): array
    {
        return [
            'autocomplete' => 'off',
            'spellcheck' => 'false',
        ];
    }
}
