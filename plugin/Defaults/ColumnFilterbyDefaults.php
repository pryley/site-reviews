<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ColumnFilterbyDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'rating' => FILTER_SANITIZE_NUMBER_INT,
            'type' => FILTER_SANITIZE_STRING,
        ];
    }
}
