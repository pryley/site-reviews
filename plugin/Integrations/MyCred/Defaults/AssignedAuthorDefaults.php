<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class AssignedAuthorDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'points' => 'int',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'log' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'log' => '%plural% for getting a review on %link_with_title%',
            'points' => 0,
        ];
    }
}
