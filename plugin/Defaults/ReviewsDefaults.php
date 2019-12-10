<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ReviewsDefaults extends Defaults
{
    /**
     * @var array
     */
    protected $mapped = [
        'count' => 'per_page', // @deprecated since v4.1.0
        'display' => 'per_page',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_to' => '',
            'category' => '',
            'offset' => '',
            'order' => 'DESC',
            'orderby' => 'date',
            'pagination' => false,
            'per_page' => 10,
            'post__in' => [],
            'post__not_in' => [],
            'rating' => '',
            'type' => '',
        ];
    }
}
