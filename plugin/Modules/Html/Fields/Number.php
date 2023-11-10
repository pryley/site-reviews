<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Number extends Field
{
    public static function defaults(string $fieldLocation = ''): array
    {
        $classes = [
            'metabox' => '',
            'setting' => 'small-text',
            'widget' => 'small-text',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }
}
