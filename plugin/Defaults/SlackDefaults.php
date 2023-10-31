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
        'header' => 'string',
    ];

    protected function defaults(): array
    {
        return [
            'assigned_links' => '',
            'header' => '',
        ];
    }
}
