<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

class Code extends Field
{
    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'textarea';
    }

    /**
     * @inheritDoc
     */
    public static function defaults()
    {
        return [
            'class' => 'large-text code',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function required()
    {
        return [
            'type' => 'textarea',
        ];
    }
}
