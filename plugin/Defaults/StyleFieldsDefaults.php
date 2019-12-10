<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class StyleFieldsDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'input' => '',
            'input_checkbox' => '',
            'input_radio' => '',
            'label' => '',
            'label_checkbox' => '',
            'label_radio' => '',
            'select' => '',
            'textarea' => '',
        ];
    }
}
