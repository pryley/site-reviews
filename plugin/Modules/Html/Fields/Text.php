<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Text extends Field
{
    /**
     * @inheritDoc
     */
    public static function defaults()
    {
        return [
            'class' => 'regular-text',
        ];
    }
}
