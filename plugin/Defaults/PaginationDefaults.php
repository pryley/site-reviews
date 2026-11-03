<?php

namespace GeminiLabs\SiteReviews\Defaults;

class PaginationDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'add_args' => 'array',
        'base' => 'string',
        'before_page_number' => 'string',
        'format' => 'string',
        'next_text' => 'string',
        'prev_text' => 'string',
        'type' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'current' => 'min:1',
        'end_size' => 'min:0',
        'mid_size' => 'min:1',
        'total' => 'min:0',
    ];

    protected function defaults(): array
    {
        return [
            'add_args' => [],
            'base' => '',
            'before_page_number' => '<span class="meta-nav screen-reader-text">'.__('Page', 'site-reviews').' </span>',
            'current' => 1,
            'end_size' => 1,
            'format' => '?'.glsr()->constant('PAGED_QUERY_VAR').'=%#%',
            'mid_size' => 1,
            'next_text' => __('Next', 'site-reviews'),
            'prev_text' => __('Previous', 'site-reviews'),
            'total' => 0,
            'type' => 'ajax',
        ];
    }

    protected function finalize(array $values = []): array
    {
        $values['current'] = max(1, min($values['current'], $values['total']));
        return $values;
    }
}
