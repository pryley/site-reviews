<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Code extends Field
{
    public static function defaults(string $fieldLocation = ''): array
    {
        $classes = [
            'metabox' => '',
            'setting' => 'large-text code',
            'widget' => '',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }

    public static function required(string $fieldLocation = ''): array
    {
        return [
            'type' => 'textarea',
        ];
    }

    public function tag(): string
    {
        return 'textarea';
    }
}
