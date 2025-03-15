<?php

namespace GeminiLabs\SiteReviews\Defaults;

class GatekeeperNoticeDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'error' => 'text',
        'name' => 'text',
        'plugin_uri' => 'url',
        'textdomain' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'error' => '',
            'name' => '',
            'plugin_uri' => '',
            'textdomain' => '',
        ];
    }
}
