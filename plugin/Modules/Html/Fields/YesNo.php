<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class YesNo extends Field
{
    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'input';
    }

    /**
     * @inheritDoc
     */
    public static function defaults()
    {
        return [
            'class' => 'inline',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function required()
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
}
