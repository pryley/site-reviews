<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ModuleStylesDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'baseOrderClass' => 'string',
        'id' => 'string',
        'name' => 'string',
        'orderClass' => 'string',
        'orderIndex' => 'int',
        'wrapperOrderClass' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'attrs' => 'array-consolidate',
        'defaultPrintedStyleAttrs' => 'array-consolidate',
        'settings' => 'array-consolidate',
        'styles' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'attrs' => [],
            'baseOrderClass' => '',
            'defaultPrintedStyleAttrs' => [],
            'elements' => null,
            'id' => '',
            'name' => '',
            'orderClass' => '',
            'orderIndex' => 1,
            'settings' => [],
            'storeInstance' => null,
            'styles' => [],
            'wrapperOrderClass' => '',
        ];
    }
}
