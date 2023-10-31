<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ToggleVerifiedDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'id' => 'int',
        'verified' => 'int',
    ];

    protected function defaults(): array
    {
        return [
            'id' => 0,
            'verified' => -1,
        ];
    }
}
