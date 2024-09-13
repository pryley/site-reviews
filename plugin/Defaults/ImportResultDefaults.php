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

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'message' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'attachments' => 0,
            'imported' => 0,
            'message' => _x('Imported %d of %d', 'admin-text', 'site-reviews'),
            'skipped' => 0,
        ];
    }
}
