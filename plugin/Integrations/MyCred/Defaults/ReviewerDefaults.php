<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class ReviewerDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'points' => 'int',
        'points_deduction' => 'int',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'log' => 'text',
        'log_deduction' => 'text',
        'per_day' => 'min:0',
        'per_post' => 'min:0',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'log' => '%plural% for writing a review',
            'log_deduction' => '%plural% deduction for deactivated / deleted review',
            'per_day' => 0,
            'per_post' => 2,
            'points' => 1,
            'points_deduction' => 1,
        ];
    }
}
