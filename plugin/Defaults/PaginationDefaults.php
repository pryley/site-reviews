<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class PaginationDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'add_args' => 'array',
        'base' => 'string',
        'before_page_number' => 'string',
        'current' => 'int',
        'end_size' => 'int',
        'format' => 'string',
        'mid_size' => 'int',
        'next_text' => 'string',
        'prev_text' => 'string',
        'total' => 'int',
        'type' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
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

    /**
     * @return array
     */
    protected function sanitize(array $values = [])
    {
        $values = parent::sanitize($values);
        $values['current'] = max(1, min($values['current'], $values['total']));
        $values['end_size'] = max(0, $values['end_size']);
        $values['mid_size'] = max(1, $values['mid_size']);
        return $values;
    }
}
