<?php

namespace GeminiLabs\SiteReviews\Defaults;

class CaptchaConfigDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'urls' => 'array',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'badge' => 'text',
        'captcha_type' => 'text',
        'class' => 'attr-class',
        'language' => 'text',
        'sitekey' => 'text',
        'size' => 'text',
        'theme' => 'text',
        'type' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'badge' => '',
            'captcha_type' => '',
            'class' => '',
            'language' => 'en',
            'sitekey' => '',
            'size' => '',
            'theme' => '',
            'type' => '',
            'urls' => [],
        ];
    }
}
