<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;

class AddonDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'description' => 'string',
        'id' => 'string',
        'link_text' => 'string',
        'plugin' => 'string',
        'slug' => 'string',
        'style' => 'string',
        'title' => 'string',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'beta' => 'bool',
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
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if (!empty($values['link_text'])) {
            return $values;
        }
        if (true === Arr::getAs('bool', $values, 'beta')) {
            $values['link_text'] = _x('Premium members only', 'admin-text', 'site-reviews');
            $values['title'] = sprintf('%s (%s)', $values['title'], _x('beta', 'admin-text', 'site-reviews'));
        } else {
            $values['link_text'] = _x('View Add-on', 'admin-text', 'site-reviews');
        }
        return $values;
    }
}
