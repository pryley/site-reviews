<?php

namespace GeminiLabs\SiteReviews\Defaults;

class UploadedFileDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'error' => 'int',
        'name' => 'string',
        'size' => 'int',
        'tmp_name' => 'string',
        'type' => 'string',
    ];

    protected function defaults(): array
    {
        return [
            'error' => -1,
            'name' => '',
            'size' => 0,
            'tmp_name' => '',
            'type' => '',
        ];
    }
}
