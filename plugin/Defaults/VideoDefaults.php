<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class VideoDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'duration' => 'string',
        'id' => 'string',
        'title' => 'string',
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
