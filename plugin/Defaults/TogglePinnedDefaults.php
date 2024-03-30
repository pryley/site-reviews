<?php

namespace GeminiLabs\SiteReviews\Defaults;

class TogglePinnedDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'pinned' => 'int',
        'post_id' => 'int',
    ];

    protected function defaults(): array
    {
        return [
            'pinned' => -1,
            'post_id' => 0,
        ];
    }
}
