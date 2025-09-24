<?php

namespace GeminiLabs\SiteReviews\Integrations\Divi\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ModuleClassnamesDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'name' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'attrs' => 'array-consolidate',
    ];

    protected function defaults(): array
    {
        return [
            'attrs' => [],
            'classnamesInstance' => null,
            'name' => '',
        ];
    }
}
