<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ColumnFilterbyDefaults extends DefaultsAbstract
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_post' => FILTER_SANITIZE_NUMBER_INT,
            'assigned_user' => FILTER_SANITIZE_NUMBER_INT,
            'author' => FILTER_SANITIZE_NUMBER_INT,
            'category' => FILTER_SANITIZE_NUMBER_INT,
            'rating' => FILTER_SANITIZE_NUMBER_INT,
            'type' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        ];
    }
}
