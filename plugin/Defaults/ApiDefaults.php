<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helper;

class ApiDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'blocking' => 'bool',
        'compress' => 'bool',
        'cookies' => 'array',
        'decompress' => 'bool',
        'force' => 'bool',
        'headers' => 'array',
        'max_retries' => 'int',
        'method' => 'string',
        'redirection' => 'int',
        'sslverify' => 'bool',
        'timeout' => 'int',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'method' => ['GET', 'POST', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'max_retries' => 'min:1|max:10',
    ];

    protected function defaults(): array
    {
        return [
            'blocking' => true,
            'body' => null,
            'compress' => false,
            'cookies' => [],
            'decompress' => true,
            'force' => false,
            'headers' => [],
            'max_retries' => 5,
            'method' => 'GET',
            'redirection' => 5,
            'sslverify' => Helper::isLocalServer(),
            'timeout' => 5,
        ];
    }
}
