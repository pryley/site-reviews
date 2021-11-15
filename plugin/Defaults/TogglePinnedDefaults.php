<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class TogglePinnedDefaults extends Defaults
{
    /**
     * @var array
     */
    public $cast = [
        'id' => 'int',
        'pinned' => 'int',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'id' => 0,
            'pinned' => -1,
        ];
    }
}
