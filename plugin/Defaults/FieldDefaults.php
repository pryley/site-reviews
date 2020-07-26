<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class FieldDefaults extends Defaults
{
    /**
     * @return array
     */
    public $casts = [
        'class' => 'string',
        'id' => 'string',
        'label' => 'string',
        'options' => 'array',
        'text' => 'string',
        'type' => 'string',
        'value' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'id' => '',
            'label' => '',
            'options' => [],
            'text' => '',
            'type' => '',
            'value' => '',
        ];
    }
}
