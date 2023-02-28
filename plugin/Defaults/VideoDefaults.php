<?php

namespace GeminiLabs\SiteReviews\Defaults;

class VideoDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
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
