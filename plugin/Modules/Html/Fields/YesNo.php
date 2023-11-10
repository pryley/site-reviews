<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class YesNo extends Field
{
    public static function defaults(string $fieldLocation = ''): array
    {
        $classes = [
            'metabox' => '',
            'setting' => 'inline',
            'widget' => 'inline',
        ];
        return [
            'class' => Arr::get($classes, $fieldLocation),
        ];
    }

    public static function required(string $fieldLocation = ''): array
    {
        return [
            'is_multi' => true,
            'options' => [
                'no' => __('No', 'site-reviews'),
                'yes' => __('Yes', 'site-reviews'),
            ],
            'type' => 'radio',
        ];
    }

    public function tag(): string
    {
        return 'input';
    }
}
