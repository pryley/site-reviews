<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class DependencyDefaults extends Defaults
{
    /**
     * @var array
     */
    public $casts = [
        'minimum_version' => 'string',
        'name' => 'string',
        'untested_version' => 'string',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'minimum_version' => 'version',
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
