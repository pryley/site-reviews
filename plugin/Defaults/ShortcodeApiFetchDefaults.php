<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class ShortcodeApiFetchDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'include' => 'array',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'include' => 'array-int',
        'option' => 'name',
        'search' => 'text',
        'shortcode' => 'name',
    ];

    protected function defaults(): array
    {
        return [
            'include' => '',
            'option' => '',
            'search' => '',
            'shortcode' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        if (is_numeric($values['search'])) {
            $values['include'][] = (int) $values['search'];
            $values['include'] = Arr::uniqueInt($values['include']);
        }
        return $values;
    }
}
