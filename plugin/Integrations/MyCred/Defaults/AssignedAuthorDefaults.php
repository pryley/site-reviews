<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class AssignedAuthorDefaults extends DefaultsAbstract
{
    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'log' => 'text',
        'log_deduction' => 'text',
        'points' => 'numeric',
        'points_deduction' => 'numeric',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'log' => '%plural% for getting a review on %link_with_title%',
            'log_deduction' => '%plural% deduction for deactivated / deleted review',
            'points' => 0,
            'points_deduction' => 0,
        ];
    }
}
