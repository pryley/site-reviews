<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ImportResultDefaults extends Defaults
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'attachments' => 'int',
        'imported' => 'int',
        'skipped' => 'int',
    ];

    protected function defaults(): array
    {
        return [
            'attachments' => 0,
            'imported' => 0,
            'skipped' => 0,
        ];
    }
}
