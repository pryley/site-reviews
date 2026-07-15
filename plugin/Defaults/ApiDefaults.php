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
        'expiration' => 'int',
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
        'expiration' => 'min:1', // ensure that there is a transient expiration
        'max_retries' => 'min:0|max:10',
        'transient_key' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'blocking' => true, // Whether the calling code requires the result of the request.
            'body' => null,
            'compress' => false, // Whether to compress the $body when sending the request.
            'cookies' => [],
            'decompress' => true, // Whether to decompress a compressed response.
            'expiration' => DAY_IN_SECONDS,
            'force' => false, // Whether to bypass any previously cached response
            'headers' => [], // Array of headers to send with the request.
            'max_retries' => 0, // The number of times a request can be retried
            'method' => 'GET',
            'redirection' => 5, // Number of allowed redirects.
            'sslverify' => !Helper::isLocalServer(), // Whether to verify SSL for the request.
            'timeout' => 5, // How long the connection should stay open in seconds.
            'transient_key' => 'request',
        ];
    }
}
