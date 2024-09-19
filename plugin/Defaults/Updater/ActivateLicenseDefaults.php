<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ActivateLicenseDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'activations_left' => 'int',
        'license_limit' => 'int',
        'site_count' => 'int',
        'success' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'checksum' => 'text',
        'error' => 'slug',
        'expires' => 'text',
        'item_name' => 'slug',
        'license' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'activations_left' => 0,
            'checksum' => '',
            'error' => '',
            'expires' => '',
            'item_name' => '',
            'license' => '',
            'license_limit' => 0,
            'site_count' => 0,
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
        }
        return $values;
    }
}
