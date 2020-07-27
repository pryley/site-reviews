<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Radio extends Checkbox
{
    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
    {
        return [
            'is_multi' => true,
            'type' => 'radio',
        ];
    }
}
