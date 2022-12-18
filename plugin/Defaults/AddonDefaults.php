<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class AddonDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'description' => 'string',
        'id' => 'string',
        'link' => 'url',
        'link_text' => 'string',
        'plugin' => 'string',
        'slug' => 'string',
        'title' => 'string',
    ];

    /**
     * @var array
     */
    public $sanitize = [
        'beta' => 'bool',
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
            'link' => '',
            'link_text' => '',
            'plugin' => '',
            'slug' => '',
            'title' => '',
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
        if (true === Cast::toBool(Arr::get($values, 'beta'))) {
            $values['link_text'] = _x('Premium members only', 'admin-text', 'site-reviews');
        } else {
            $values['link_text'] = _x('View Add-on', 'admin-text', 'site-reviews');
        }
        return $values;
    }
}
