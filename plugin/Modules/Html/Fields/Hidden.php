<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Hidden extends Field
{
    public static function required(string $fieldLocation = ''): array
    {
        return [
            'is_raw' => true,
        ];
    }
}
