<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class AddonDefaults extends Defaults
{
  /**
     * The values that should be sanitized.
     * This is done after $casts and $enums.
     * @var array
     */
    public $sanitize = [
        'beta' => 'bool',
        'description' => 'text',
        'id' => 'id',
        'link_text' => 'text',
        'plugin' => 'text',
        'slug' => 'slug',
        'style' => 'attr-style',
        'title' => 'text',
        'url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
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
     * @return array
     */
    protected function finalize(array $values = [])
    {
        if (!empty($values['link_text'])) {
            return $values;
        }
        if (true === $values['beta']) {
            $values['link_text'] = _x('Premium members only', 'admin-text', 'site-reviews');
            $values['title'] = sprintf('%s (%s)', $values['title'], _x('beta', 'admin-text', 'site-reviews'));
        } else {
            $values['link_text'] = _x('View Add-on', 'admin-text', 'site-reviews');
        }
        return $values;
    }
}
