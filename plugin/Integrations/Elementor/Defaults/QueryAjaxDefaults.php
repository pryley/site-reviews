<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract;

class QueryAjaxDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'post_id' => 'int',
    ];

    /**
     * The keys that should be mapped to other keys.
     * Keys are mapped before the values are normalized and sanitized.
     * Note: Mapped keys should not be included in the defaults!
     */
    public array $mapped = [
        'editor_post_id' => 'post_id',
        'id' => 'include',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'include' => 'text',
        'option' => 'name',
        'search' => 'text',
        'shortcode' => 'name',
    ];

    protected function defaults(): array
    {
        return [
            'include' => '',
            'option' => '',
            'post_id' => '',
            'search' => '',
            'shortcode' => '',
        ];
    }
}
