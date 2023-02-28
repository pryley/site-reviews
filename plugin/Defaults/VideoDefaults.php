<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class VideoDefaults extends Defaults
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and $enums.
     * @return array
     */
    public $sanitize = [
        'duration' => 'regex:/[^\d\:]/',
        'id' => 'regex:/[^\w\-]/',
        'title' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'duration' => '',
            'id' => '',
            'title' => '',
        ];
    }
}
