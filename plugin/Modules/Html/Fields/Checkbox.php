<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Checkbox extends Field
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
            'value' => 1,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function required()
    {
        return [
            'is_multi' => true,
            'type' => 'checkbox',
        ];
    }
}
