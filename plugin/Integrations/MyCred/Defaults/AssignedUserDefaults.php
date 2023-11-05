<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class AssignedUserDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'log' => 'text',
        'log_deduction' => 'text',
        'points' => 'numeric',
        'points_deduction' => 'numeric',
    ];

    protected function defaults(): array
    {
        return [
            'log' => '%plural% for getting a review',
            'log_deduction' => '%plural% deduction for deactivated / deleted review',
            'points' => 0,
            'points_deduction' => 0,
        ];
    }
}
