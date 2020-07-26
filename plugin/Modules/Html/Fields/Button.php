<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Button extends Field
{
    /**
     * @inheritDoc
     */
    public static function defaults($fieldLocation = null)
    {
        return [
            'class' => 'button',
        ];
    }
}
