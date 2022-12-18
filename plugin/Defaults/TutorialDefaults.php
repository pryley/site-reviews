<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TutorialDefaults extends Defaults
{
    /**
     * @return array
     */
    public $sanitize = [
        'videos' => 'array',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'videos' => [],
        ];
    }
}
