<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class StyleClassesDefaults extends Defaults
{
    /**
     * @var string[]
     */
    public $concatenated = [
        'label',
        'field',
        'form',
    ];

    /**
     * @var string
     */
    public $glue = ' ';

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'label' => 'glsr-label',
            'field' => 'glsr-field',
            'form' => 'glsr-form',
        ];
    }
}
