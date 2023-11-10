<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Radio extends Checkbox
{
    public static function required(string $fieldLocation = ''): array
    {
        return [
            'is_multi' => true,
            'type' => 'radio',
        ];
    }
}
