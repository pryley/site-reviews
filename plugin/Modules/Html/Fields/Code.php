<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helpers\Arr;

class Code extends Field
{
    /**
     * @inheritDoc
     */
    public static function defaults($fieldLocation = null)
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

    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
    {
        return [
            'type' => 'textarea',
        ];
    }

    /**
     * @inheritDoc
     */
    public function tag()
    {
        return 'textarea';
    }
}
