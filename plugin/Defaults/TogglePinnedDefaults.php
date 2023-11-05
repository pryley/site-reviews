<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TogglePinnedDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'id' => 'int',
        'pinned' => 'int',
    ];

    protected function defaults(): array
    {
        return [
            'id' => 0,
            'pinned' => -1,
        ];
    }
}
