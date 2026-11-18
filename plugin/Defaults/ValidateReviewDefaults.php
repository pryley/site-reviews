<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ValidateReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'content' => 'string',
        'email' => 'string',
        'name' => 'string',
        'title' => 'string',
    ];

    protected function defaults(): array
    {
        return [
            'content' => '',
            'email' => '',
            'form_id' => '',
            'ip_address' => '',
            'name' => '',
            'rating' => '0',
            'terms' => '',
            'title' => '',
        ];
    }
}
