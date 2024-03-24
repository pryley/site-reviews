<?php

namespace GeminiLabs\SiteReviews\Defaults;

class PointerDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'position' => 'array',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'content' => 'text',
        'id' => 'attr',
        'screen' => 'attr',
        'target' => 'attr',
        'title' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'content' => '',
            'id' => '',
            'position' => [
                'edge' => 'right',
                'align' => 'middle',
            ],
            'screen' => glsr()->post_type,
            'target' => '',
            'title' => '',
        ];
    }
}
