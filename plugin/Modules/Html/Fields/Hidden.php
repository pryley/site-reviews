<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Hidden extends Field
{
    /**
     * @return array
     */
    public static function required()
    {
        return [
            'is_raw' => true,
        ];
    }
}
