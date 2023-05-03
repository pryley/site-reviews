<?php

namespace GeminiLabs\SiteReviews\Defaults;

class SlackDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'assigned_links' => 'string',
        'edit_url' => 'string',
        'header' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'edit_url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_links' => '',
            'edit_url' => '',
            'header' => '',
        ];
    }
}
