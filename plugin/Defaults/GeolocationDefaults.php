<?php

namespace GeminiLabs\SiteReviews\Defaults;

class GeolocationDefaults extends DefaultsAbstract
{
    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'status' => [
            'fail', 'success',
        ],
    ];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        'message', 'status',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'continentCode' => 'continent',
        'countryCode' => 'country',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'city' => 'text',
        'continentCode' => 'text',
        'countryCode' => 'text',
        'message' => 'text',
        'region' => 'text',
        'status' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'city' => '',
            'continent' => '',
            'country' => '',
            'message' => '', // included only when status is fail
            'region' => '',
            'status' => 'fail',
        ];
    }
}
