<?php

namespace GeminiLabs\SiteReviews\Defaults\Updater;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class DeactivateLicenseDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'success' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'checksum' => 'text',
        'expires' => 'date',
        'item_name' => 'slug',
        'license' => 'slug',
    ];

    protected function defaults(): array
    {
        return [
            'checksum' => '',
            'expires' => '',
            'item_name' => '',
            'license' => '',
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
