<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Checkbox extends Field
{
    /**
     * @inheritDoc
     */
    public static function required($fieldLocation = null)
    {
        return [
            'is_multi' => true,
            'type' => 'checkbox',
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
