<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ModuleScriptDataDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'id' => 'string',
        'name' => 'string',
        'selector' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'animation' => 'array-consolidate',
        'attrs' => 'array-consolidate',
        'background' => 'array-consolidate',
        'interactions' => 'array-consolidate',
        'link' => 'array-consolidate',
        'scroll' => 'array-consolidate',
        'sticky' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'animation' => [],
            'attrs' => [],
            'background' => [],
            'elements' => null,
            'id' => '',
            'interactions' => [],
            'link' => [],
            'name' => '',
            'scroll' => [],
            'selector' => '',
            'sticky' => [],
            'storeInstance' => null,
        ];
    }
}
