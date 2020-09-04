<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Button extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function defaults($fieldLocation = null)
    {
        return [
            'class' => 'button',
        ];
    }
}
