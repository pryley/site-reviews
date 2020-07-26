<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Select extends Field
{
    /**
     * @inheritDoc
     */
    public static function defaults($fieldLocation = null)
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
