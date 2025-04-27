<?php

namespace GeminiLabs\SiteReviews\Defaults;

class StatDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'ID' => 'int',
        'rating_id' => 'int',
    ];

    /**
     * The values that should be guarded.
     *
     * @var string[]
     */
    public array $guarded = [
        'ID',
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
        'continent' => 'text',
        'country' => 'text',
        'region' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'city' => '',
            'continent' => '',
            'country' => '',
            'ID' => 0,
            'rating_id' => 0,
            'region' => '',
        ];
    }
}
