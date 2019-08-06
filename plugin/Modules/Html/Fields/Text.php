<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Text extends Field
{
    /**
     * @return array
     */
    public static function defaults()
    {
        return [
            'class' => 'regular-text',
        ];
    }
}
