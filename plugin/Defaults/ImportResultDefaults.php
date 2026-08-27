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
        'duplicates' => 'int',
        'failed' => 'int',
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
        // skipped is the total; duplicates and failed count the reasons inside it.
        return [
            'attachments' => 0,
            'duplicates' => 0,
            'failed' => 0,
            'imported' => 0,
            /* translators: %1$d: number of records imported, %2$d: total number of records */
            'message' => _x('Imported %1$d of %2$d', 'admin-text', 'site-reviews'),
            'skipped' => 0,
        ];
    }
}
