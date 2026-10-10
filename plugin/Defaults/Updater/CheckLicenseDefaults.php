<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class CheckLicenseDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'activations_left' => 'int',
        'is_premium_license' => 'bool',
        'license_limit' => 'int',
        'lifetime' => 'bool',
        'site_count' => 'int',
        'success' => 'bool',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'license' => [
            'disabled',
            'expired',
            'inactive',
            'invalid_item_id',
            'item_name_mismatch',
            'site_inactive',
            'valid',
        ],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'checksum' => 'text',
        'connect_url' => 'url',
        'expires' => 'date',
        'item_name' => 'slug',
        'license' => 'slug',
        'renewal_url' => 'url',
        'subscription' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'activations_left' => 0,
            'checksum' => '',
            'connect_url' => '',
            'expires' => '',
            'item_name' => '',
            'is_premium_license' => false,
            'license' => 'invalid',
            'license_limit' => 0,
            'lifetime' => false,
            'renewal_url' => '',
            'site_count' => 0,
            'subscription' => '',
            'success' => false,
        ];
    }

    /**
     * Normalize provided values, this always runs first.
     */
    protected function normalize(array $values = []): array
    {
        if (empty($values['expires'])) {
            return $values;
        }
        if ('lifetime' === $values['expires']) {
            $values['expires'] = date('Y-m-d H:i:s', strtotime('+10 years'));
            $values['lifetime'] = true;
        }
        return $values;
    }
}
