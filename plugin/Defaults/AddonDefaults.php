<?php

namespace GeminiLabs\SiteReviews\Defaults;

class AddonDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'beta' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'description' => 'text',
        'id' => 'id',
        'link_text' => 'text',
        'plugin' => 'text',
        'slug' => 'slug',
        'style' => 'attr-style',
        'title' => 'text',
        'url' => 'url',
    ];

    protected function defaults(): array
    {
        return [
            'beta' => false,
            'description' => '',
            'id' => '',
            'link_text' => '',
            'plugin' => '',
            'slug' => '',
            'style' => '',
            'title' => '',
            'url' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        if (!empty($values['link_text'])) {
            return $values;
        }
        if (true === $values['beta']) {
            $values['link_text'] = _x('Premium members only', 'admin-text', 'site-reviews');
            $values['title'] = sprintf('%s (%s)', $values['title'], _x('beta', 'admin-text', 'site-reviews'));
        } else {
            $values['link_text'] = _x('View addon', 'admin-text', 'site-reviews');
        }
        return $values;
    }
}
