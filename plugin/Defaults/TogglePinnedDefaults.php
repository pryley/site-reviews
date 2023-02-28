<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TogglePinnedDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
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
