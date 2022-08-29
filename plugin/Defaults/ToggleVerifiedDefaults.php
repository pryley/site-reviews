<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ToggleVerifiedDefaults extends Defaults
{
    /**
     * @var array
     */
    public $cast = [
        'id' => 'int',
        'verified' => 'int',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'id' => 0,
            'verified' => -1,
        ];
    }
}
