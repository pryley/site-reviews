<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class FieldDefaults extends Defaults
{
    public $cast = [
        'options' => 'array',
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
