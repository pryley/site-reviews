<?php

namespace GeminiLabs\SiteReviews\Defaults;

class FlyoutItemDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'icon' => 'attr-class',
        'title' => 'text',
        'url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'icon' => '',
            'title' => '',
            'url' => '',
        ];
    }
}
