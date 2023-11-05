<?php

namespace GeminiLabs\SiteReviews\Defaults;

class DependencyDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'minimum_version' => 'version',
        'name' => 'text',
        'plugin_uri' => 'url',
        'untested_version' => 'version',
    ];

    protected function defaults(): array
    {
        return [
            'minimum_version' => '',
            'name' => '',
            'plugin_uri' => '',
            'untested_version' => '',
        ];
    }
}
