<?php

namespace GeminiLabs\SiteReviews\Defaults;

class DependencyDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'minimum_version' => 'version',
        'name' => 'text',
        'plugin_uri' => 'url',
        'untested_version' => 'version',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'minimum_version' => '',
            'name' => '',
            'plugin_uri' => '',
            'untested_version' => '',
        ];
    }
}
