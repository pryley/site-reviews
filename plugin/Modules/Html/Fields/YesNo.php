<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class YesNo extends Field
{
    /**
     * @inheritDoc
     */
    public static function defaults($fieldLocation = null)
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

    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
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

    /**
     * @inheritDoc
     */
    public function tag()
    {
        return 'input';
    }
}
