<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Select extends Field
{
    public static function defaults(string $fieldLocation = ''): array
    {
        $classes = [
            'metabox' => '',
            'setting' => '',
            'widget' => 'widefat',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }
}
